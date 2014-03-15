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
                $this->logger()->debug(array('Bound %s', $anots['route']));
                $this->router()->map($anots['route'], $method->getClosure($this));
            }
            if (isset($anots['event'])) {
                $this->logger()->debug(array('Event %s', $anots['event']));
                $this->event()->on($anots['event'], $method->getClosure($this));
            }
        });
    }
}
