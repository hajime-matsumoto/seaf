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
 * @SeafDataTableName test
 * @SeafDataIndex name=name
 * @SeafDataIndex name=age
 * @SeafDataPrimary name=name
 */
class UserPre extends Model
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
        $db->connectionMap([
            'sql' => 'mysql://root:deganjue@localhost:3306/seaf_test',
            'nosql' => 'mongodb://localhost:27017/test'
        ]);
        $db->tableMap(['test' => 'nosql']);
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

        $user = User::table( )->findOne(['name'=>'hajime']);
        $this->assertEquals(
            'hajime',$user->name
        );
        $this->assertEquals(
            32, $user->age
        );

        foreach (User::table( )->find(['name'=>'hajime']) as $user) {
            $this->assertEquals(
                'hajime',$user->name
            );
        }
    }

    public function testUserPre ( )
    {
        $db = $this->getHandler( );
        UserPre::tableInitialize( );

        UserPre::create([
            'name' => 'sosuke',
            'age' => 3
        ])->save();

        $m = UserPre::table()->findOne(['name'=>'sosuke']);
        $this->assertEquals(
            'sosuke', $m->name
        );
        $this->assertEquals(
            3, $m->age
        );
    }
}
