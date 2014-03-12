<?php
namespace Seaf\Web;

use Seaf\App\Application as BaseApplication;
use Seaf\Core\Kernel;

/**
 * WEBアプリケーションクラス
 */
class Application extends BaseApplication
{
    private $descs = array();

    public function initApplication ( )
    {
        parent::initApplication();

        // アノテーションを解析する
        Kernel::ReflectionClass($this)->mapAnnotation(function($method, $anots){
            if (isset($anots['route'])) {
                $this->route($anots['route'], $method->getClosure($this));
            }
            if (isset($anots['event'])) {
                $this->on($anots['event'], $method->getClosure($this));
            }
        });

        $this->on('before.run', function( ) {
            ob_start();
        })->on('after.run', function($req, $res) {
            $contents = ob_get_clean();
            $res->status(200)->write($contents)->send();
        });

        $this->request()->init();
    }

    public function _notfound($body = null, $code = '404')
    {
        $this->response()
            ->status(404)
            ->write('<h1>404 Not Found</h1>')
            ->write($body)
            ->send();
    }
}
