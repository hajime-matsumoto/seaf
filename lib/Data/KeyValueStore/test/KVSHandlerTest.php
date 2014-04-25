<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

class KVSHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * インスタンス生成可能か
     */
    public function testFactory ( )
    {
        $kvs = $this->getHandler();

        $this->assertInstanceOf(
            'Seaf\Data\KeyValueStore\KVSHandler',
            $kvs
        );
    }

    private function getHandler( )
    {
        $kvs = KVSHandler::factory([
            'component' => [
                'FileSystem' => [
                    'rootPath' => '/tmp/seaf'
                ],
                'Memcache' => [
                    'servers' => [
                        'localhost'
                    ]
                ]
            ]
        ]);
        return $kvs;
    }

    /**
     * CRUD
     */
    public function testFileSystemCRUD( )
    {
        $kvs = $this->getHandler()->table('default');

        $kvs->set($k='はじめのファイル', $orig_data = [1,2,3,4], $orig_stat = ['test'=>true]);
        $data = $kvs->get($k, $status);

        $this->assertEquals($orig_data, $data);
        $this->assertEquals($status['test'], true);

        $this->assertTrue($kvs->has($k));

        $kvs->del($k);

        $this->assertFalse($kvs->has($k));
    }

    /**
     * MEMCACHE CRUD
     */
    public function testMemCacheCRUD( )
    {
        $kvs = $this->getHandler()->table('default', 'memcache');

        $kvs->set($k='はじめのファイル', $orig_data = [1,2,3,4], $orig_stat = ['test'=>true]);
        $data = $kvs->get($k, $status);

        $this->assertEquals($orig_data, $data);
        $this->assertEquals($status['test'], true);

        $this->assertTrue($kvs->has($k));

        $kvs->del($k);

        $this->assertFalse($kvs->has($k));
    }
}
