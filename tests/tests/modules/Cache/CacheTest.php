<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Cache;

use Seaf;

/**
 * Cacheのテスト
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * スタートアップ
     */
    protected function setUp()
    {
        Seaf::enmod('cache');
    }

    /**
     * シャットダウン
     */
    protected function tearDown()
    {
    }

    public function testStandard ( )
    {
        // モジュールを取得する
        $cache = Seaf::Cache();

        // 全データを削除する
        $cache->flush();

        $this->assertFalse(
            $cache->get(1)
        );

        // キャッシュを設定する
        $cache->set(1, $_SERVER, time() + 1);

        // キャッシュを取得する
        $result = $cache->get(1);

        // 期限切れのテスト
        sleep(1);
        $this->assertFalse($cache->get(1));
    }
}
