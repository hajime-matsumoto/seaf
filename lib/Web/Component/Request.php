<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web\Component;

use Seaf\BackEnd;
use Seaf\Util\Util;
use Seaf\Web;
use Seaf\Base\DI;
use Seaf\Base\Event;
use Seaf\Base\Types;
use Seaf\Logging;


/**
 * WEBリクエスト管理
 */
class Request implements Web\ComponentIF
{
    use Web\ComponentTrait;

    private $url;

    public function __construct( )
    {
        // グローバル変数取得用コンテナの取得
        $g = BackEnd()->system->superGlobals->container();
        // URLオブジェクトを初期化する
        $protcol = $g->isEmpty($g->get('_SERVER.HTTPS')) ? 'http': 'https';
        $host    = $g->get('_SERVER.HTTP_HOST', 'localhost:80');
        $uri     = $g->get('_SERVER.REQUEST_URI', '/');

        $this->url()->init($protcol."://".$host.$uri);
    }


    public function url ( )
    {
        if (!$this->url) {
            $this->url = new Types\URL();
        }
        return $this->url;
    }

    public function getPath( )
    {
        return $this->url()->toPath();
    }

    public function __clone( )
    {
        $this->url = clone $this->url;
    }
}
