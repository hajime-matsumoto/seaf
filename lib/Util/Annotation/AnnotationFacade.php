<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * キャッシュモジュール
 */
namespace Seaf\Util\Annotation;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Module;

/**
 * モジュールファサード
 */
class AnnotationFacade extends Module\ModuleFacade
{
    /**
     * アノテーションコンテナをビルドする
     */
    public function build($class)
    {
        if (!is_string($class)) $class= get_class($class);

        $isDebug = $this->module('registry')->isDebug();

        $container = $this->rootParent( )->cache->annotation->useCache(
            $class, function ( ) use ($class) {
                return new AnnotationContainer($class);
            }, 0, $isDebug ? time()-5: 0
        );

        return $container;
    }
}
