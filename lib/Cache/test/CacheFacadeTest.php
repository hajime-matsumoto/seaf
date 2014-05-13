<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

class CacheFacadeTest extends \PHPUnit_Framework_TestCase
{
    private $listen;

    public function testCache ( )
    {
        $cache = new CacheFacade( );
        $cache->on('log', function ($e) {
            echo $e->log."\n";
        });

        $data = $cache->useCache(1,function() {
            return "aaa";
        }, 1, 0);

        $data = $cache->a->b->c->useCache(1,function() {
            return "aaa";
        }, 1, 0);

        $data = $cache->useCache(1,function() {
            return "aaa";
        }, 1, 0);

        $this->assertEquals(
            'aaa', $data
        );
    }
}
