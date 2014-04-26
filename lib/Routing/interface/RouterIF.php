<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Routing;

use Seaf\Com\Request;

/**
 * ルータクラス
 */
interface RouterIF
{
    /**
     * ルーティングする
     */
    public function route (Request\Request $req);
}
