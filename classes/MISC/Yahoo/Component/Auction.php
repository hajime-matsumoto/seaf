<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\MISC\Yahoo\Component;

use Seaf\MISC\Yahoo;
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

    /**
     * コンストラクタ
     */
    public function __construct ($cfg)
    {
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

        return new Yahoo\Auction\Item($res['ResultSet']['Result']);
    }

    /**
     * 入札中のアイテムを取得する
     */
    public function getMyStatusBidding ( )
    {
        foreach ($this->Env->getUsers() as $user) {
            var_dump($user->login());
        }
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
        $item = $this->auctionItem($auction_id);

        foreach ($this->Env->getUsers() as $user) 
        {
            // 成功するまでユーザを切り替えて実行
            if ($this->bidFixedUser($user, $auction_id, $price, $amount, $account, $code))
            {
                break;
            }
        }

    }

    public function bidFixedUser ($user, $auction_id, $price, $amount, &$account, &$code)
    {
        $item = $this->auctionItem($auction_id);

        $account = null;
        $code = null;

        // ログインさせる
        $user->login();

        // アイテムページを見てみる
        $data = $user->client( )->get($item->getAuctionItemUrl());
        file_put_contents('/tmp/item', $data);

        // アイテムページが取得できなかったら
        if (empty($data)) {
            $code = 'ERROR_CANT_GET_ITEM_PAGE';
            return false;
        }


        // プレビューフォームを取得
        // HINT action="http://pageinfo3.auctions.yahoo.co.jp/jp/show/bid_preview"
        $regex = '#action="http://([^\.]+)\.auctions\.yahoo\.co\.jp/jp/show/bid_preview"#';

        if(!preg_match($regex, $data)) {
            // プレビューへのフォームがなかったら
            $code = 'ERROR_CANT_GET_BID_PREVIEW_FORM';
            return false;
        }

        // 入札可能な状態だったら
        // スクレイピングしてLoginURLとパラメタを取得する
        $html = HTML\Parser::parse($data);
        $form = $html->find('form[id=frmbb1]', 0);
        // パラメタを解析
        $params = [];
        foreach ($form->find('input') as $input) {
            if ($input->type == 'submit') continue;
            $params[$input->name] = $input->value;
        }
        // パラメタを上書き
        $params['Quantity'] = $amount;
        $params['Bid']      = $price;
        $params['md5']      = "1";

        // プレビューフォームへリクエストを送信する
        $body = $user->client( )->init()->post(
            $form->action,
            $params
        );
        file_put_contents('/tmp/confirm', mb_convert_encoding($body, 'utf-8', 'eucjp'));

        if (empty($body)) {
            $code = 'ERROR_REQUEST_TO_PREVIEW_FAILD';
            return false;
        }


        // 入札フォームを取得 
        // HINT action="http://bid3.auctions.yahoo.co.jp/jp/config/placebid"
        $regex = '|action="(http://[^\.]+\.auctions\.yahoo\.co\.jp/jp/config/placebid)"|';
        if(!preg_match($regex, $body, $m)) {
            $code = 'ERROR_CANT_GET_BID_FORM';
            return false;
        }

        // 確認フォームを取得
        $html = HTML\Parser::parse($body);
        $form = $html->find('form[action='.$m[1].']', 0);

        // パラメタを解析
        $params = [];
        foreach ($form->find('input') as $input) {
            if ($input->type == 'submit') continue;
            $params[$input->name] = $input->value;
        }

        // 入札フォームへリクエストを送信する
        // @TODO LOGGING
        $body = $user->client( )->init()->post(
            $form->action,
            $params
        );

        $body = mb_convert_encoding($body, 'utf-8', 'eucjp');
        file_put_contents('/tmp/finish', $body);

        if (empty($body)) {
            $code = 'ERROR_REQUEST_TO_BID_FAILD';
            return false;
        }

        // アラートボックスの取得
        $html = HTML\Parser::parse($body);
        foreach (['modAlertBox','modInfoBox'] as $cand) {
            $msgBox = $html->find('div[id='.$cand.']', 0);
            if (!empty($msgBox)) {
                break;
            }
        }
        if (empty($msgBox)) {
            die('CANT_FIND_MESSAGE_BOX');
        }
        $msg = trim($msgBox->find('strong', 0)->plaintext);

        // 入札を受け付けました。あなたが現在の最高額入札者です。
        if (
            $msg 
            == 
            "入札を受け付けました。あなたが現在の最高額入札者です。"
        ) {
            $account = $user->account;
            return true;
        }elseif(
            $msg 
            == 
            "申し訳ありません。入札する金額は、現在の入札額よりも高い値を設定してください。"
        ) {
            $code = "ERROR_BID_PRICE_IS_TOO_CHEEPE";
            return false;
        }else{
            die('UNKNOWN_MESSAGE('.$msg.')');
        }
    }
}
