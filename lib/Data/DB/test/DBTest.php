<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Data;

/**
 * DB Test
 */
class DBTest extends \PHPUnit_Framework_TestCase
{
    public function testMongoDBConnection ( )
    {
        $db = new DBHandler ( );
        $db->setDefaultConnectionName('nosql');
        $db->connectionMap(['nosql' => 'mongodb://localhost:27017/test']);
        $db->tableMap(['test' => 'nosql']);

        $db->test->drop();
        foreach ([10,20,30,40,50,60] as $num) {
            $db->test->insert(['name' => 'hajime'.$num, 'age' => $num]);
        }

        // 検索をする
        $findQuery = $db->test->find([
            '$or' => [
                ['age'=>['$gt'=>20]],
                ['age'=>['$lt'=>50]]
            ]
        ])
        ->sort(array('age'=>FindQuery::SORT_DESC))
        ->limit(2)
        ->offset(1);

        $ans = [50,40];
        $idx = 0;
        foreach ($findQuery as $data)
        {
            $this->assertEquals(
                $data['age'], $ans[$idx++]
            );
        }
    }

    public function testMysqlDBConnection ( )
    {
        $db = new DBHandler ( );
        $db->setDefaultConnectionName('sql');
        $db->connectionMap(['sql' => 'mysql://root:deganjue@localhost:3306/seaf_test']);
        $db->tableMap(['test' => 'sql']);

        $db->test->drop();
        $db->test->create([
            'fields' => [
                'name' => [
                    'type'=>'varchar',
                    'length' => 100
                ],
                'age' => [
                    'type' => 'int',
                    'length' => 4
                ]
            ]
        ]);
        foreach ([10,20,30,40,50,60] as $num) {
            $db->test->insert(['name' => 'hajime'.$num, 'age' => $num]);
        }

        // 検索をする
        $findQuery = $db->test->find([
            '$or' => [
                ['age'=>['$gt'=>20]],
                ['age'=>['$lt'=>50]]
            ]
        ])
        ->sort(array('age'=>FindQuery::SORT_DESC))
        ->limit(2)
        ->offset(1);

        $ans = [50,40];
        $idx = 0;
        foreach ($findQuery as $data)
        {
            $this->assertEquals(
                $data['age'], $ans[$idx++]
            );
        }
    }
}
