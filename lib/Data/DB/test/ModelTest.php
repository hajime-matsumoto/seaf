<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Data;

/**
 * @SeafDataTableName users
 * @SeafDataIndex name=name
 * @SeafDataIndex name=age
 * @SeafDataPrimary name=name
 */
class User extends Model
{
    /**
     * @SeafDataAttrs type=varchar&length=100
     */
    protected $name;

    /**
     * @SeafDataAttrs type=int&length=4
     */
    protected $age;

    /**
     * 遅延束縛
     */
    public static function who ( )
    {
        return __CLASS__;
    }
}

/**
 * DB Test
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
    public function getHandler( ) 
    {
        $db = new DBHandler ( );
        $db->setDefaultConnectionName('sql');
        $db->connectionMap(['sql' => 'mysql://root:deganjue@localhost:3306/seaf_test']);
        $db->tableMap(['test' => 'sql']);
        $db->swapSingleton();
        return $db;
    }

    public function testCreateModel ( )
    {
        $db = $this->getHandler();
        User::tableInitialize();

        $model = User::create([
            'name' => 'hajime',
            'age' => 31
        ]);
        $model->save();

        // 更新
        $model->age = 32;
        $model->save();

        var_dump(
            User::table( )->findOne(['name'=>'hajime'])
        );
        foreach (User::table( )->setMethod('outputFilter', function ($m) {
            return $m['name'];
        })->find(['name'=>'hajime']) as $m) {
            var_dump($m);
        }
    }
}
