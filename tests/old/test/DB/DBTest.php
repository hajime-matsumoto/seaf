<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;
use Seaf\Util\ArrayHelper;

class DBTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->g = ArrayHelper::getClosure('get');
    }

    protected function tearDown()
    {
    }

    public function testDSN ( )
    {
        $g = $this->g;

        $dsn = new DSN('mysql://root:deganjue@localhost:3306/mysql');
        $this->assertEquals(
            'mysql',
            $g($dsn->parse(), 'db')
        );

        $dsn = new DSN('sqlite:///var/db');
        $this->assertEquals(
            '/var/db',
            $g($dsn->parse(), 'db')
        );
    }

    public function testSqlBuilder ( )
    {
        $handler = DB::connect('mysql://root:deganjue@localhost:3306/mysql');
        $sql = new SqlBuilder('SELECT * FROM test WHERE id = :id');
        $sql->setHandler($handler);
        $sql->declearColumn(':id', DB::DATA_TYPE_INT);
        $sql->bindValue(':id', 1);
        $this->assertEquals(
            'SELECT * FROM test WHERE id = 1',
            $sql->buildSql()
        );
    }


    public function testMysqlConnect ( )
    {
        $handler = DB::connect('mysql://root:deganjue@localhost:3306/mysql');

        // エラーになるクエリ
        $result = $handler->query('SHOW TBLES');
        $this->assertTrue(
            $result->isError()
        );

        // エラーにならないクエリ
        $result = $handler->query('SHOW TABLES');
        $this->assertFalse(
            $result->isError()
        );

        // 結果を取得する
        $datas = $result->fetchAssocAll( );
        $this->assertTrue(
            count($datas) > 20
        );
    }

    /**
     * プリペアステートメントのテスト
     */
    public function testPreparedStatment ( )
    {
        $db = DB::connect('mysql://root:deganjue@localhost:3306/seaf_test');
        $db->execute ('DROP TABLE IF EXISTS test;');
        $db->execute ('CREATE TABLE IF NOT EXISTS test (id int, name varchar(100));');

        $stat = $db->prepare ('INSERT INTO test (id, name) VALUES (:id, :name)');
        $stat->bindValue(':id', 1, DB::DATA_TYPE_INT);
        $stat->bindValue(':name', 'hajime', DB::DATA_TYPE_STR);

        $this->assertEquals(
            'INSERT INTO test (id, name) VALUES (1, "hajime")',
            $stat->buildSql()
        );
        $result = $stat->execute( );

        $stat = $db->prepare('SELECT id, name FROM test WHERE id = :id');
        $stat->bindValue(':id', 1, DB::DATA_TYPE_INT);
        $result = $stat->execute();
        foreach($result as $row) {
            $this->assertEquals(
                'hajime',
                $row['name']
            );
        }

        $stat = $db->prepare('SELECT id, name FROM test WHERE id = :id');
        $stat->declear(':id', DB::DATA_TYPE_INT);
        $result = $stat->execute(['id'=>1]);
        $this->assertEquals(
            'hajime',
            $result->getCols('name')
        );
    }

    /**
     * テーブルとモデルのテスト
     */
    public function testTable ( )
    {
        $db = DB::connect('mysql://root:deganjue@localhost:3306/seaf_test');
        $db->execute('DROP TABLE IF EXISTS test;');
        $db->execute('CREATE TABLE IF NOT EXISTS test (id int, name varchar(100));');

        // データの追加
        $stat = $db->prepare ('INSERT INTO test (id, name) VALUES (:id, :name)');
        $stat->bindValue(':id', 1, DB::DATA_TYPE_INT);
        $stat->bindValue(':name', 'hajime', DB::DATA_TYPE_STR);
        $stat->execute();

        // 定義
        $table = new Table ( );
        $table->setHandler($db);
        $table->setTableName('test');
        $table->declearColumn('id', DB::DATA_TYPE_INT, 4);
        $table->declearColumn('name', DB::DATA_TYPE_STR, 100);
        $table->declearPrimaryKey('id');

        // 取得
        $result = $table->get(1);
    }

    /**
     * テーブルのテスト
     */
    public function testModel ( )
    {
        $db = DB::connect('mysql://root:deganjue@localhost:3306/seaf_test');
        $db->execute('DROP TABLE IF EXISTS test;');
        $db->execute('CREATE TABLE IF NOT EXISTS test (id int, name varchar(100));');

        // 定義
        $table = new Table ( );
        $table->setHandler($db);
        $table->setTableName('test');
        $table->declearColumn('id', DB::DATA_TYPE_INT, 4);
        $table->declearColumn('name', DB::DATA_TYPE_STR, 100);
        $table->declearPrimaryKey('id');
        $table->useModel(__NAMESPACE__.'\\Model');

        // 取得
        $model = $table->get(1);

        // モデルを取得
        $model = $table->create();
        $model->id = 2;
        $model->name = 'matsumoto';
        $model->save();

        // モデルを更新
        $model->name = 'shibuya';
        $model->save();

        // モデルをロード
        $model = $table->get(2);
        $this->assertEquals(
            'shibuya',
            $model->name
        );
    }

    /**
     * モデルベーススキーマのテスト
     */
    public function testModelBasedSchema ( )
    {
        $db = DB::connect('mysql://root:deganjue@localhost:3306/seaf_test');

        // 定義
        $table = new Table ('Model\UserPre');
        $table->setHandler($db);
        $db->dropTableBySchema(\Model\UserPre::schema());
        $db->createTableBySchema(\Model\UserPre::schema());

        $model = $table->create();
        $model->regKey = '1';
        $model->regDate = time();
        $model->save();
    }

    /**
     * トランザクションハンドラのテスト
     */
    public function testTransactionHandler ( )
    {
        $db = DB::connect('mysql://root:deganjue@localhost:3306/seaf_test');
        $th = new TransactionHandler();
        $th->setDBHandler('default', $db);

        // 定義
        $table = new Table ('Model\UserPre');
        $table->setHandler($th);
        $db->dropTableBySchema(\Model\UserPre::schema());
        $db->createTableBySchema(\Model\UserPre::schema());

        $model = $table->create();
        $model->regKey = '1';
        $model->regDate = time();
        $model->save();

        $result = $table->select( )->expire(time() + 2)->result();
        $this->assertInstanceOf(
            'Seaf\DB\Result',
            $result
        );
        $result = $table->select( )->result();
        $this->assertInstanceOf(
            'Seaf\DB\CacheableResult',
            $result
        );
        $result = $table->select( )->nocache()->result();
        $this->assertFalse($result instanceof CacheableResult);
    }
}
