<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Kvs;

use Seaf;

/**
 * Kvsのテスト
 */
class KvsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * スタートアップ
     */
    protected function setUp()
    {
        Seaf::enmod('kvs');
    }

    /**
     * シャットダウン
     */
    protected function tearDown()
    {
    }

    public function testStandard ( )
    {
        // KVSを取得する
        $kvs = Seaf::Kvs();

        // 全データを削除する
        $kvs->flush();

        // 値を設定する
        $kvs->set(1, $_SERVER);

        // 値を取得する
        $result = $kvs->get(1);

        // 値とステータスを取得する
        $result = $kvs->get(1, $stat);

        $this->assertTrue(time() >= $stat['created']);

        // 値を削除する
        $kvs->del(1);
        $this->assertFalse($kvs->get(1));
    }
}
