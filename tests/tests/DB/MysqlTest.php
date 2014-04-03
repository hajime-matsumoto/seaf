<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;

class MysqlTest extends \PHPUnit_Framework_TestCase
{
    public function testConnectionTest ( )
    {
        $handler = Seaf::DB();

        // 設定があっているか？
        $this->assertInstanceOf(
            'Seaf\DB\DataSource\Mysql',
            $handler('sql')
        );
    }

    public function testCreateTable ( )
    {
        $result = Seaf::DB('sql')->newRequest('query')->body(
            'DROP TABLE IF EXISTS mysql_test;
             CREATE TABLE IF NOT EXISTS mysql_test (id int, name varchar(100));'
        )->execute();

        $this->assertFalse($result->isError());
    }

    public function testInsertRequest ( )
    {
        // インサートリクエストを取得
        $req = Seaf::DB('sql')->mysql_test->insert();
        $this->assertInstanceOf(
            'Seaf\DB\Request\InsertRequest', $req
        );

        
        Seaf::DB('sql')->query('BEGIN');
        foreach(range(1,20) as $id) {
            $req->param('id',$id)->param('name','hajime'.$id)->execute();
        }
        Seaf::DB('sql')->query('COMMIT');
    }

    public function testUpdateRequest ( )
    {
        // 更新リクエストを取得
        $req = Seaf::DB('sql')->mysql_test->update();
        $this->assertInstanceOf(
            'Seaf\DB\Request\UpdateRequest', $req
        );
        $result = $req
            ->param('name','hajime_updated')
            ->where(['id'=>1])
            ->limit(1)
            ->order('id')
            ->execute();
        $this->assertFalse($result->isError());
    }

    public function testFindRequest ( )
    {
        // 検索リクエストを実行
        $result = Seaf::DB('sql')->mysql_test
            ->find()
            ->limit(5)
            ->order('id')
            ->execute();
        $this->assertEquals(
            $result->fetch('object')->name,'hajime_updated'
        );
        $this->assertEquals(
            5,
            count($result)
        );
    }

    public function testDeleteRequest ( )
    {
        // 削除リクエストを実行
        $result = Seaf::DB('sql')->mysql_test
            ->delete()
            ->where(['id'=>1])
            ->limit(1)
            ->order('id')
            ->execute();
        $this->assertFalse($result->isError());
    }
}
