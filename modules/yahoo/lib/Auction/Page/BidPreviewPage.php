<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Yahoo\Auction\Page;

use Seaf\Module\Yahoo;
use Seaf\Module\Yahoo\Auction\Auction;
use Seaf\Module\Yahoo\Account\User;
use Seaf\Net\HTTP;
use Seaf\DOM\HTML;
use Seaf\Base;

/**
 * 入札プレビューページ
 */
class BidPreviewPage
{
    private $Auction;
    private $User;
    public $body;
    private $action;
    private $params;

    public function __construct(Auction $Auction, User $User, $action, $params, $body)
    {
        $this->Auction = $Auction;
        $this->User = $User;
        $this->body = $body;
        $this->action = $action;
        $this->params = $params;
    }

    public static function retrive(Auction $Auction, User $User, $action, $params, &$page)
    {
        $body = $User
            ->client( )
            ->init( )
            ->post($action, $params);

        // アイテムページが取得できなかった
        if (empty($body)) return $Auction->raiseError(
            'CANT_GET_BID_PREVIEW_PAGE', [
                'yahoo_id' => $User->account,
                'action' => $action,
                'params' => $params
            ]
        );

        // 変換
        $body = mb_convert_encoding($body, 'utf-8', 'eucjp');

        // 入札者制限
        if (
            preg_match(
                "/このオークションは、出品者によって入札者認証制限が設定されています。/",
                $body,
                $m
            )
        ) {
            return $Auction->raiseError(
                'ACCOUNT_REFUSED_COUSE_OF_RESTRICTION', [
                    'cose' => $m[0],
                    'body' => $body
                ]
            );
        }

        $page = new BidPreviewPage($Auction, $User, $action, $params, $body);
        return true;
    }

    /**
     * 入札プレビューページから入札フォームとパラメタを取得
     *
     * @param string
     * @param string
     * @param array
     */
    public function findBidForm(&$action, &$params)
    {
        $body = $this->body;

        // 入札フォームを取得 
        // HINT action="http://bid3.auctions.yahoo.co.jp/jp/config/placebid"
        $regex = '|action="(http://[^\.]+\.auctions\.yahoo\.co\.jp/jp/config/placebid)"|';
        if(!preg_match($regex, $body, $m)) {
            $this->Auction->raiseError(
                'CANT_FIND_BID_FORM', [
                    'yahoo_id' => $this->User->account,
                    'action' => $this->action,
                    'params' => $this->params,
                    'html' => $this->body
                ]
            );
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

        $action = $form->action;

        return true;
    }
}
