<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;

class StartUPTest extends \PHPUnit_Framework_TestCase
{
    private $handler;

    protected function setUp()
    {
        $this->handler = Handler::factory([
            'setting' => [
                'default_connection' => 'sql'
            ],
            'connectMap' => [
                'nosql' => 'mongo:///test',
                'sql'   => 'mysql://root:deganjue@localhost:3306/seaf_test'
            ],
            'handlerMap' => [
                'access_log' => 'nosql',
                'user_pre'   => ['sql', 'UserPreModel::schema()']
            ],
            'cache' => [
                'storage' => [
                    'type'    => 'memcache',
                    'servers' => ['localhost:11211']
                ]
            ]
        ]);
    }

    protected function tearDown()
    {
    }

    /**
     * クエリ
     */
    public function testQueryRequest ( )
    {
        $handler = $this->handler;
        $request = $handler->newRequest('query')->body('SHOW TABLES');
        $result = $request->execute();
        $result->setClass('Seaf\Data\Container\ArrayContainer');
    }

    /**
     * キャッシュクエリ
     */
    public function testCachedRequest ( )
    {
        $handler = $this->handler;

        $request = $handler->newRequest('query');
        $request->body('SHOW TABLES')->cache(time() + 2);
        $result = $request->execute();
        $pre = $result->fetch();
        $this->assertFalse(
            $result->getCacheStatus()
        );
        $result = $request->execute();
        $aft = $result->fetch();
        $this->assertRegexp('/Hit/',$result->getCacheStatus());
        $this->assertEquals(
            $pre, $aft
        );
    }

    /**
     * Seaf Coreから取得
     */
    public function testAsSeafComponent( )
    {
        $db = Seaf::DB( );
        $recs = $db->newRequest('query')->body('show tables')->execute()->fetchAll();
        $org_recs = $this->handler->newRequest('query')->body('show tables')->execute()->fetchAll();
        $this->assertEquals(
            $recs,
            $org_recs
        );
    }
}
