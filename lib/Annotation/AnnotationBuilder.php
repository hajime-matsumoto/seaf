<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Annotation;

use Seaf\Wrapper;
use Seaf\Base;
use Seaf\Cache;
use Seaf\Registry;

class AnnotationBuilder
{
    use Base\SingletonTrait;
    use Cache\CacheUserTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * アノテーションコンテナを作成する
     */
    public static function build ($class) 
    {
        return static::getSingleton( )->_build($class);
    }

    public function __construct ( )
    {
    }

    public function _build($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $cache = $this->getCacheHandler()->section('class.annotation');
        $key = $class;

        $container = $cache->useCacheIf(
            Registry\Registry::isProduction(),
            $key,
            function (&$isSuccess) use ($class) {
                $container = new AnnotationContainer($class);
                $isSuccess = true;
                return $container;
            },
            0,
            0
        );
        return $container;
    }
}
