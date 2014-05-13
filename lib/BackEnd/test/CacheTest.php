<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

/**
 * キャッシュモジュールのテスト
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest ( )
    {
        $m = Manager::getSingleton();

        // CACHEモジュール
        $cache = $m->cache;

        $cache->test->key1->key2->useCache(111, function(&$s, $key) use(&$used) {
            $used = $key;
            $s = true;
            return 'aaa';
        }, 1, 0);

        $this->assertEquals('cache.test.key1.key2.111', $used);
    }

}
