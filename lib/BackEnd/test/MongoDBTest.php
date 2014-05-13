<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

use Seaf\Util\Util;

/**
 * データベースモジュールのテスト
 */
class MongoDatabaseTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic ( )
    {
        $m = Manager::getSingleton();
        $m->registry->phpRegister();

        // データベースモジュール
        $db = $m->database;

        // データソースをセットする
        $db->setDatasource('default', 'mongodb://localhost:27017/test');

        // テーブルをセットする
        $db->setTable(['test' => ['default', 'テスト用のテーブル']]);

        // 解説
        $db->explain();

        // テーブルへのリクエストを作成する
        $this->assertInstanceof(
            'Seaf\Database\ProxyRequest\TableFindRequest',
            $db->test->find()
        );

        // Database[sql][test] テーブルを構築する
        $db->test->declear( )
            ->field('id', 'int', 4)
            ->field('name', 'string', 128)
            ->field('email', 'string', 128)
            ->field('place', 'string', 128)
            ->field('age', 'int')
            ->index('name')
            ->primary_index('id')
            ->option('useDrop', true)
            ->option('useAutoIncrement', true)
            ->create()
        ;

        $db->test->insert([
            'name' => 'hajime',
            'email' => 'mail@avap.co.jp',
            'place' => 'tokyo',
            'age' => 31
        ]);

        $db->test->insert([
            'name' => 'lina',
            'email' => 'lina@avap',
            'place' => 'osaka',
            'age' => 21
        ]);
        $db->test->insert([
            'name' => 'sosuke',
            'email' => 'sosuke@avap',
            'place' => 'mongol',
            'age' => 3
        ]);
        $db->test->insert([
            'name' => 'taizo',
            'email' => 'taizo@avap',
            'place' => 'tokyo',
            'age' => 29
        ]);
        $db->test->insert([
            'name' => 'komata',
            'email' => 'komata@avap',
            'place' => 'tokyo',
            'age' => 28
        ]);
        $db->test->insert([
            'name' => 'yuka',
            'email' => 'yuka@avap',
            'place' => 'tokyo',
            'age' => 27
        ]);

        // 検索リクエストの作成と実行
        $query = $db->test->find([
            '$or' => [
                ['place' => 'tokyo'],
                ['place' => 'mongol']
            ]
        ])->sort(['age'=>1])->limit(1);

        $query->setFetchMode('assoc');
        $query->setFetchFilter(function($data) {
            return Util::Dictionary($data);
        });
        $this->assertEquals(
            'sosuke', $query->fetch()->name
        );


        // 更新クエリ
        $table = $db->test;

        $table->update([
            'name' => 'Hajime MATSUMOTO',
            'age' => 31
        ],['name'=>'hajime']);

        $table->update([
            'name' => 'Sousuke Hajime MATSUMOTO',
            'age' => 3
        ],['name'=>'sosuke']);

        $data = $table->find([
            '$or' => [
                ['age'=>['$gt'=>29]],
                ['age'=>['$lt'=>4]]
            ]
        ])->sort(['age'=>-1]);

        $rows = [];

        foreach($data as $r)
        {
            $rows[] = $r;
        }

        $this->assertEquals(
            'Hajime MATSUMOTO',
            $rows[0]['name']
        );
        $this->assertEquals(
            'Sousuke Hajime MATSUMOTO',
            $rows[1]['name']
        );
    }

}
