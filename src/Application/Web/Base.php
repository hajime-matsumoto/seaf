<?php
namespace Seaf\Application\Web;

use Seaf\Application\Base as ApplicationBase;
use Seaf\Kernel\Kernel;

/**
 * Webアプリケーション
 */
class Base extends ApplicationBase
{
    /**
     * 初期化処理
     */
    public function initApplication ()
    {
        $this->environment->di()->addComponentNamespace(__CLASS__, '\\Component');

        // アノテーションバインディング
        Kernel::ReflectionClass($this)->mapAnnotation(function($method, $anots){
            if (isset($anots['route'])) {
                if (!is_array($anots['route'])) {
                    $anots['route'] = array($anots['route']);
                }
                foreach ($anots['route'] as $route) {
                    $this->logger()->debug(array('Bound %s', $route));
                    $this->router()->map($route, $method->getClosure($this));
                }
            }
            if (isset($anots['event'])) {
                $this->logger()->debug(array('Event %s', $anots['event']));
                $this->event()->on($anots['event'], $method->getClosure($this));
            }
        });

        $this->environment->bind($this,
            array(
                'redirect' => '_redirect'
            )
        );

        $this->initWeb();
    }

    /**
     * Web用の初期化メソッド
     */
    public function initWeb( )
    {
    }

    /**
     * リダイレクト
     */
    public function _redirect ($uri)
    {
        $uri = $this->request()->uri->getAbs($uri);
        $this->response()
            ->status(303)
            ->header('Location', $uri)
            ->send();
    }
}
