<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateSchema ( )
    {
        $schema = new Schema( );
        $schema
            ->table('test')
            ->field('id', 'int', 4, null, false)
            ->field('name', 'str', 100, '', true)
            ->index('name_idx', 'name', false, 100)
            ->primary('id')
            ->autoIncrement(true);

        // DropTableしてCreateTableする
        $result = Seaf::DB('sql')->createTable($schema, true);

        $this->assertTrue($result);
    }

    /**
     * データモデルからスキーマを生成する
     */
    public function testDataModelSchema ( )
    {
        $schema = Schema::createByModel('Model\UserPre');
        $result = Seaf::DB('sql')->createTable($schema, true);
        $this->assertTrue($result);
    }
}
