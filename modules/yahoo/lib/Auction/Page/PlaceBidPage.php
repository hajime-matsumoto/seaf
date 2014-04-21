<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Yahoo\Auction\Page;

use Seaf\Module\Yahoo;
use Seaf\Module\Yahoo\Auction\Auction;
use Seaf\Module\Yahoo\Account\User;
use Seaf\Net\HTTP;
use Seaf\DOM\HTML;
use Seaf\Base;

/**
 * 入札ページ
 */
class PlaceBidPage
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
            'CANT_GET_PLACE_BID_PAGE', [
                'yahoo_id' => $User->account,
                'action' => $action,
                'params' => $params
            ]
        );

        // 変換
        $body = mb_convert_encoding($body, 'utf-8', 'eucjp');
        $page = new PlaceBidPage($Auction, $User, $action, $params, $body);

        return true;
    }

    /**
     * 入札ページからメッセージを取得する
     *
     * @param string
     * @param string
     * @param array
     */
    public function findMessage(&$msg)
    {
        $body = $this->body;

        // アラートボックスの取得
        $html = HTML\Parser::parse($body);
        foreach (['modAlertBox','modInfoBox'] as $cand) {
            $msgBox = $html->find('div[id='.$cand.']', 0);
            if (!empty($msgBox)) {
                break;
            }
        }

        if (empty($msgBox)) {
            return $this->raiseError(
                'CANT_FIND_MESSAGE_BOX', [
                    'action' => $this->action,
                    'params' => $this->params,
                    'html' => $this->body
                ]
            );
        }

        $msg = trim($msgBox->find('strong', 0)->plaintext);

        return true;
    }
}
