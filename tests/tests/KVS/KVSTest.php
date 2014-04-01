<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\KVS;

use Seaf;

class KVSTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testDSN ( )
    {
    }

    public function testFileSystemStorage ( )
    {
        $storage = Storage::factory([
            'type' => 'fileSystem',
            'path' => '/tmp/seaf.test'
        ]);
        $storage->put('key', 'value');
        $this->assertEquals(
            'value',
            $storage->get('key')
        );
    }

    public function testMemcacheStorage ( )
    {
        $storage = Storage::factory([
            'type' => 'memcache',
            'servers' => ['localhost:11211']
        ]);

        $storage->put('key', 'value');
        $this->assertEquals(
            'value',
            $storage->get('key')
        );
    }
}
