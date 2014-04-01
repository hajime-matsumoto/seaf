<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Pattern;
use Seaf\Cache\CacheHandler;

/**
 * データベース
 */
class Cache extends CacheHandler
{
    /**
     * 作成するメソッド
     *
     * @param array
     */
    public static function componentFactory ( )
    {
        $cache = new self();
        $c = Seaf::Config('cache.storage', array());
        $cache->setStorage($c);
        return $cache;
    }
}
