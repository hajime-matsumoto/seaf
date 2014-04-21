<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Yahoo\Auction;

use Seaf\Module\Yahoo;
use Seaf\Net\HTTP;
use Seaf\DOM\HTML;
use Seaf\Base;

/**
 * オークションコンポーネント
 */
class Auction
{
    const DUMMY_USER_AGENT = 'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20100101 Firefox/12.0';

    const API_URL          = 'http://auctions.yahooapis.jp/AuctionWebService';
    const API_VERSION      = 'V2';
    const API_ITEM         = 'auctionItem';

    private $Env;
    public $lastError;

    // @TODO メッセージのコードと出現箇所の対応
	const YahooWinAuction      = "おめでとうございます!!　あなたが落札しました";
	const YahooHightestBidder1 = "あなたが現在の最高額入札者です";
	const YahooHightestBidder2 = "落札結果を確認し、今後の取引の進め方を、出品者と相談してください";
	const YahooBidFail1        = "出品者が設定した最低落札価格よりも低い金額です";
	const YahooBidFail2        = "入札できませんでした。有効な入札金額を入力してください";
	const YahooBidFail3        = "Yahoo!オークション - 再入札";
	const YahooBidFail4        = "申し訳ありません";
	const YahooBidFail5        = "入札する権限がありません";
	const YahooBidFail6        = "この出品者のオークションへの入札はできません";
	const YahooBidFail7        = 'あなたの入札価格を上回る入札が行われました';
	const YahooBidFail8        = '現在の入札額よりも高い値を設定してください';
	const YahooBidFail9        = '現在の価格の100倍以上の金額は入札できません';
	const YahooBidFail10       = 'あなたの評価は出品者の要求する評価を満たしていません';
	const YahooBidFail11       = 'スタークラブのランク獲得者';
	const YahooBidFail12       = '数量の条件を確認して再入札を行うことをご検討ください';
	const YahooBidFail13       = '今後この商品には入札できません';
	const YahooBidFail14       = '価格を下げて入札しなおしてください';
	const YahooBidFail15       = '入札数が制限を越えました';
	const YahooBidFail16       = 'オークションの利用を停止されています';
	const YahooBidFail17       = '特定のお客様からのアクセスを一時的に制限させて頂いています';
	const YahooBidFail18       = 'あなたのブラックリストに登録されているため、入札できません';

    private static $placeBidMessageMap = [
        "入札を受け付けました。あなたが現在の最高額入札者です。"
        => "YOUR_HIGHEST_BIDDER",
        "申し訳ありません。入札する金額は、現在の入札額よりも高い値を設定してください。"
        => "BID_PRICE_IS_TOO_CHEEPE",
        "申し訳ありません。自動入札であなたの入札価格を上回る入札が行われました。"
        => "SOMEBODY_HAS_HIGHER_BID_BY_AUTO_BID"
    ];

    /**
     * コンストラクタ
     */
    public function __construct ($cfg = [])
    {
    }

    /**
     * エラーを起す
     */
    public function raiseError ($code, $params)
    {
        $this->lastError['code'] = $code;
        $this->lastError['params'] = $params;
        return false;
    }
 
    /**
     * Yahoo Environmentを設定する
     *
     * @param Yahoo\Environment $Env
     */
    public function acceptYahooEnvironment(Yahoo\Environment $Env)
    {
        $this->Env = $Env;
    }

    /**
     * オークションアイテムの検索
     *
     * @param string
     */
    public function auctionItem ($auction_id)
    {
        // クライアントを生成
        $client = HTTP\Client::factory([
            'agent' => self::DUMMY_USER_AGENT
        ]);

        $res = $client->init( )->get(
            self::API_URL.'/'.self::API_VERSION.'/auctionItem', [
                'appid'     => $this->Env->selectApiUser( )->id,
                'output'    => 'php',
                'auctionID' => (string) $auction_id
            ]
        );

        $res = unserialize($res);
        if (false === $res) return false;

        if (!isset($res['ResultSet'])) return false;
        if (1 !== (int) $res['ResultSet']['totalResultsAvailable']) return false;

        return new Yahoo\Auction\Model\Item($res['ResultSet']['Result']);
    }


    /**
     * 入札する
     *
     * @param string
     * @param int
     * @param int
     * @param ref 実行したアカウント名
     * @param ref 実行結果コード
     */
    public function bid ($auction_id, $price, $amount, &$account, &$code)
    {
        $this->lastError = [];

        $item = $this->auctionItem($auction_id);

        foreach ($this->Env->getUsers() as $user) 
        {
            // 成功するまでユーザを切り替えて実行
            if ($this->bidFixedUser($user, $auction_id, $price, $amount, $account, $code))
            {
                break;
            }
        }

        if (!empty($this->lastError)) {
            // オークション終了のステータスをとる
            if (in_array(
                $this->lastError['code'],
                ['CANT_FIND_PREVIEW_FORM']
            )) {
                $code = 'AUCTION_CLOSED';

            }else if (in_array( // 入札失敗のステータスをとる(リトライ不可能)
                $this->lastError['code'],
                ['ACCOUNT_REFUSED_COUSE_OF_RESTRICTION']
            )) {
                $code = 'BID_FAIL';
            }else{
                $code = $this->lastError['code'];
            }

            file_put_contents('/tmp/log', print_r($this->lastError, true));
        }

    }


    /**
     * YahooIDを固定して入札する
     *
     * @param string
     * @param int
     * @param int
     * @param ref 実行したアカウント名
     * @param ref 実行結果コード
     */
    public function bidFixedUser ($user, $auction_id, $price, $amount, &$account, &$code)
    {
        $item = $this->auctionItem($auction_id);

        $account = null;
        $code    = null;


        // ログインしていなければログインする
        $user->login();

        // アイテムページを取得
        if(!$this->retriveItemPage($user, $auction_id, $itemPage)) {
            return false;
        }

        // アイテムページからプレビューフォームを探す
        if(!$itemPage->findPreviewForm($action, $params)) {
            return false;
        }

        // 見つかったパラメタに追加する
        $params = array_merge($params, [
            'Quantity' => $amount,
            'Bid'      => $price,
            'md5'      => 1
        ]);

        // プレビューページを取得
        if (!$this->retriveBidPreviewPage($user, $action, $params, $BidPreviewPage)) {
            return false;
        }

        // 入札フォームを探す
        if(!$BidPreviewPage->findBidForm($action, $params)) {
            return false;
        }

        // 入札する
        if (!$this->retrivePlaceBidPage($user, $action, $params, $PlaceBidPage)) {
            return false;
        }

        // Yahooアカウントを保存しておく
        $account = $user->account;

        // 入札ステータスメッセージを取得する
        if(!$PlaceBidPage->findMessage($msg)) {
            return false;
        }

        // 入札ステータスメッセージをコードに変換する
        if(!$this->placeBidMessageToCode($msg, $code)) {
            $this->raiseError(
                'CANT_CONVERT_BID_MESSAGE_TO_CODE', [
                    'auction_id' => $auction_id,
                    'msg'        => $msg
                ]
            );
        }

        return in_array($msg, ['YOUR_HIGHEST_BIDDER']) ? true: false;
    }

    // -----------------------------------------------------------
    // ページ取得系
    // -----------------------------------------------------------

    /**
     * アイテムページを取得する
     *
     * @param User
     * @param string
     * @param Page\ItemPage
     * @return bool
     */
    public function retriveItemPage($user, $auction_id, &$itemPage)
    {
        return Page\ItemPage::retrive(
            $this,
            $user,
            $auction_id,
            $itemPage
        );
    }

    /**
     * 入札プレビューページを取得する
     *
     * @param User
     * @param string
     * @param array
     * @param Page\BidPreviewPage
     * @return bool
     */
    public function retriveBidPreviewPage($user, $action, $params, &$BidPreviewPage)
    {
        return Page\BidPreviewPage::retrive(
            $this,
            $user,
            $action,
            $params,
            $BidPreviewPage
        );
    }

    /**
     * 入札ページを取得する
     *
     * @param User
     * @param string
     * @param array
     * @param Page\PlaceBidPage
     * @return bool
     */
    public function retrivePlaceBidPage($user, $action, $params, &$PlaceBidPage)
    {
        return Page\PlaceBidPage::retrive(
            $this,
            $user,
            $action,
            $params,
            $PlaceBidPage
        );
    }

    // -----------------------------------------------------------
    // utility
    // -----------------------------------------------------------

    /**
     * メッセージからコードを作成します
     */
    public function placeBidMessageToCode($msg, &$code)
    {

        if (isset(self::$placeBidMessageMap[$msg])) {
            $code = self::$placeBidMessageMap[$msg];
            return true;
        }

        return false;
    }
}
