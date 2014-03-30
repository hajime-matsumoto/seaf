<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\DB;

use Seaf;

/**
 * DBのテスト
 */
class DBTest extends \PHPUnit_Framework_TestCase
{
    /**
     * スタートアップ
     */
    protected function setUp()
    {
        Seaf::enmod('db');
    }

    /**
     * シャットダウン
     */
    protected function tearDown()
    {
    }

    public function testSqlite ( )
    {
        // DBを取得する
        $db = Seaf::DB();

        // SQLite用のハンドラを取得
        $sqlite = $db('sqlite');

        $sqlite->execute('DROP TABLE IF EXISTS Test');
        $sqlite->execute('CREATE TABLE IF NOT EXISTS Test(id)');

        $pre = $sqlite->prepare('INSERT INTO TEST(id) VALUES (:id)');

        $sqlite->beginTransaction();
        $pre->bindValue(':id', 1, 'int')->execute();
        $pre->bindValue(':id', 2, 'int')->execute();
        $pre->bindValue(':id', 3, 'int')->execute();
        $sqlite->commit();

        $res = $sqlite->prepare('SELECT * FROM TEST')->execute();
        $cnt = 1;
        while($recode = $res->fetch()) {
            $this->assertEquals(
                $cnt,
                $recode['id']
            );
            $cnt++;
        }
    }
}
