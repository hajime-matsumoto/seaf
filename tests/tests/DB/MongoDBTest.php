<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;

class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    public function testConnectionTest ( )
    {
        $handler = Seaf::DB();

        // 設定があっているか？
        $handler = Seaf::DB()->open('nosql');
        $this->assertInstanceOf(
            'Seaf\DB\DataSource\Mongo',
            $handler
        );

        // シンタックスシュガー
        $this->assertInstanceOf(
            'Seaf\DB\DataSource\Mongo',
            Seaf::DB('nosql')
        );
    }

    public function testInsertRequest ( )
    {
        // アクセスログへの追加リクエストを作成
        $id = 0;
        while (10>$id) {
            $id++;

            $req = Seaf::DB()->access_log->newRequest('insert')->param([
                'user_id'=>$id,
                'path'=>'/index'
            ]);

            // リクエストを実行
            $result = $req->execute();
        }

        $this->assertFalse(
            $result->isError()
        );
    }

    public function testFindRequest ( )
    {
        // アクセスログへの検索リクエストを作成
        $req = Seaf::DB()->access_log->newRequest('find')->param([
            'user_id'=>1
        ]);

        // リクエストを実行
        $result = $req->execute();
        $this->assertTrue(
            count($result->fetchAll()) > 1
        );
    }

    public function testUpdateRequest ( )
    {
        // アクセスログへの更新を実行
        Seaf::DB()->access_log->update( )
            ->param(['status'=>0])
            ->where(array('user_id'=>1))
            ->execute();

        $res = Seaf::DB()->access_log->find( )
            ->where(['user_id' => 1])
            ->limit(10)
            ->order('user_id')
            ->execute();

        foreach($res->fetchAll() as $line) {
            $this->assertEquals(
                $line['status'],
                0
            );
        }


    }
    public function testMapReduce ( )
    {
        // アクセスログへのコマンドリクエストを作成
        $req = Seaf::DB('nosql')->newRequest('command')->param([
            'mapreduce' => 'access_log',
            'map' => 'function() {emit({uid:this.user_id,path:this.path}, 1);}',
            'reduce' => 'function(k, vals){ '.
                'var sum = 0;'.
                'for (var i in vals) { '.
                'sum += vals[i];'.
                '}'.
                'return sum;}',
            'out' => array('merge'=>'accessCounts')
        ]);

        // リクエストを実行
        $result = $req->execute();
        $this->assertTrue(
            count($all = $result->fetchAll('object')) > 10
        );
    }
}
