<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\App;
use Seaf\Core\Environment;

/**
 * WEBアプリケーションリクエスト
 * ===========================
 */
class RequestComponent extends App\Component\RequestComponent
{
    /**
     * init
     *
     * @param $config = falss
     * @return void
     */
    public function initDefault() 
    {
        // リクエストを作る
        $SERVER    = Kernel::rg()->get('SERVER');
        $REQUEST   = Kernel::rg()->get('REQUEST');
        $uri       = $SERVER['REQUEST_URI'];
        $base      = dirname($SERVER['SCRIPT_NAME']);
        $path_info = dirname($SERVER['PATH_INFO']);

        if ($base != "" && $base != '/' && strpos($uri, $base) === 0) {
            $uri = substr($uri,strlen($base));
        }

        $parts  = parse_url($uri);
        $uri    = $parts['path'];
        $params = array();

        if (isset($parts['query'])) {
            parse_str($parts['query'], $params);
        }

        // メソッドを取得
        if (isset($SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($REQUEST['_method'])) {
            $method = $REQUEST['_method'];
        } elseif (isset($SERVER['REQUEST_METHOD'])) {
            $method = $SERVER['REQUEST_METHOD'];
        } else {
            $method ='GET';
        }

        if (!empty($path_info)) {
            $uri = $path_info;
        }

        $this->init(array(
            'method'=>$method,
            'uri'=> $uri
        ));
    }
}
