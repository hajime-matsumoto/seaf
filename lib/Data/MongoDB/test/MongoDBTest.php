<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\MongoDB;

use Seaf\Data;

/**
 * MongoDB Test
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    public function testConnection ( )
    {
        $dsn = new Data\DSN('mongodb://localhost:27017/test');
        $mongodb = new MongoDBHandler($dsn);

        $result = $mongodb->test->insert([
            'name' => 'hajime'
        ]);

        $result = $mongodb->test->drop();
        $this->assertFalse($result->isError());
        $result = $mongodb->test->insert([
            'name' => 'matsumoto',
            'place' => 'meguro',
            'age' => 20
        ]);
        $result = $mongodb->test->insert([
            'name' => 'hajime',
            'place' => 'meguro',
            'age' => 10
        ]);
        $result = $mongodb->test->insert([
            'name' => 'taizo',
            'place' => 'hiratsuka',
            'age' => 40
        ]);
        $result = $mongodb->test->insert([
            'name' => 'shibuya',
            'place' => 'hiratsuka',
            'age' => 30
        ]);
        $this->assertFalse($result->isError());
        $result = $mongodb->test->ensureIndex(['id'=>true]);
        $this->assertFalse($result->isError());

        // 検索をする
        $findQuery = $mongodb->test->find([
            '$or' => [
                ['age'=>10],
                ['age'=>20],
                ['age'=>30]
            ]
        ])
        ->sort(array('age'=>FindQuery::SORT_DESC))
        ->limit(2)
        ->offset(1);

        foreach($findQuery as $data) {
            $this->assertTrue($data['age'] < 21);
        }
    }
}
