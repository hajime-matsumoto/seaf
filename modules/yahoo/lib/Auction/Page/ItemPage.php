<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Yahoo\Auction\Page;

use Seaf\Module\Yahoo;
use Seaf\Module\Yahoo\Auction\Auction;
use Seaf\Module\Yahoo\Account\User;
use Seaf\Net\HTTP;
use Seaf\DOM\HTML;
use Seaf\Base;

/**
 * アイテムページ
 */
class ItemPage
{
    private $Auction;
    private $User;
    public $body;

    public function __construct(Auction $Auction, User $User, $body, $auction_id)
    {
        $this->body = $body;
        $this->Auction = $Auction;
        $this->User = $User;
        $this->auctionID = $auction_id;
    }

    public static function retrive(Auction $Auction, User $User, $auction_id, &$page)
    {
        $body = $User
            ->client( )
            ->init( )
            ->get($Auction->auctionItem($auction_id)->getAuctionItemUrl());

        // アイテムページが取得できなかった
        if (empty($body)) return $Auction->raiseError(
            'CANT_GET_ITEM_PAGE', [
                'yahoo_id' => $User->account,
                'auction_id' => $auction_id
            ]
        );

        $page = new ItemPage($Auction, $User, $body, $auction_id);
        return true;
    }

    /**
     * アイテムページからプレビューフォームとパラメタを取得
     *
     * @param string
     */
    public function findPreviewForm(&$action, &$params)
    {
        $body = $this->body;

        // プレビューフォームを取得
        // HINT action="http://pageinfo3.auctions.yahoo.co.jp/jp/show/bid_preview"
        $regex = '#action="http://([^\.]+)\.auctions\.yahoo\.co\.jp/jp/show/bid_preview"#';
        if(!preg_match($regex, $body)) {
            return $this->Auction->raiseError(
                'CANT_FIND_PREVIEW_FORM',[
                    'yahoo_id' => $this->User->account,
                    'auction_id' => $this->auctionID,
                    'html' => $body
                ]
            );
        }

        // 入札可能な状態だったら
        $html = HTML\Parser::parse($body);
        $form = $html->find('form[id=frmbb1]', 0);

        // 返却用にアクションの値を格納
        $action = $form->action;

        // パラメタを解析
        $params = [];
        foreach ($form->find('input') as $input) {
            if ($input->type == 'submit') continue;
            $params[$input->name] = $input->value;
        }

        return true;
    }
}
