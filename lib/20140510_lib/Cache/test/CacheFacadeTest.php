<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;


class CacheFacadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testCreateCache ( )
    {
        $facade = new CacheFacade($config = [
            'strategy' => [
                'type' => 'memcache',
                'servers' => ['localhost:11211'],
                'prefix'  => 'test'
            ]
        ]);

        // ログを記録する
        $facade->on('log', function($e) {
            echo "LOG=".$e->getParam('log')->getMessage()." ";
            echo "FROM:".get_class($e('target'));
            echo "\n";
        });

        // キャッシュを作る
        $facade->createCache(1,"a",10);

        // キャッシュを取得
        $data = $facade->retriveCache(1);

        $this->assertEquals('a', $data);

        // キャッシュを破棄
        $facade->destroyCache(1);

        // キャッシュを取得
        $data = $facade->retriveCache(1);
        $this->assertFalse($data);
    }

    /**
     * 
     */
    public function testUseCache ( )
    {
        $facade = new CacheFacade($config = [
            'strategy' => [
                'type' => 'memcache',
                'servers' => ['localhost:11211'],
                'prefix'  => 'test'
            ]
        ]);

        // ログを記録する
        $facade->on('log', function($e) {
            echo "LOG=".$e->getParam('log')->getMessage()." ";
            echo "FROM:".get_class($e('target'));
            echo "\n";
        });

        $sec = 1;

        // キャッシュを使う
        $this->assertEquals(
            'aaa', 
            $facade->useCache(1,function(&$s) {
                $s = true;
                return "aaa";
            }, $sec, 0)
        );
        $this->assertEquals(
            'aaa', 
            $facade->useCache(1,function(&$s) {
                $s = true;
                return "aaa";
            }, $sec, 0)
        );

        sleep($sec);
        $this->assertEquals(
            'aaa', 
            $facade->useCache(1,function(&$s) {
                $s = true;
                return "aaa";
            }, $sec, 0)
        );

    }
}
