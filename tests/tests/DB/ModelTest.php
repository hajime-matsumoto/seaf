<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;
use Model;

class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * モデルからテーブルを作成する
     */
    public function testCreateTable ( )
    {
        $schema = Model\UserPre::schema( );
        $result = Seaf::DB('sql')->createTable($schema, true);
        $this->assertTrue($result);

        Seaf::DB('sql')->createTable(Model\User::schema(), true);
    }

    /**
     * セッターのテスト
     */
    public function testSetterTable ( )
    {
        $model = new Model\UserPre( );
        $model->regKey = 1;
        $this->assertTrue($model->regKey == sha1(1));
    }

    /**
     * 値の変更通知のテスト
     */
    public function testModifiedParams ( )
    {
        $model = new Model\UserPre( );
        $model->regHost = '1.1.1.1';
        $model->rebaseParams();
        $model->regKey = 1;
        $model->regTime = time();
        $this->assertTrue(
            count($model->modifiedParams()) == 2
        );
    }

    /**
     * 保存のテスト
     */
    public function testModelSave ( )
    {
        $model = new Model\UserPre( );
        $model->regKey = 1;
        $model->regTime = time();
        $model->save( );
        $model->regTime = time()+100;
        $model->save( );
    }

    /**
     * オートインクリメントのテスト
     */
    public function testModelAutoIncrement ( )
    {
        $model = new Model\User( );
        $model->name = 'hajime';
        $model->save( );

        $model = new Model\User( );
        $model->name = 'matsu';
        $model->save( );
        $this->assertEquals(2, $model->id);
    }

    /**
     * アクティブレコードのテスト
     */
    public function testActiveRecode ( )
    {
        $result = Model\User::select( )->
            where([
                'user_id ='=>[1,'int'],
                'and',
                'name ='=>['hajime','str']
            ])->execute();

        $this->assertEquals('hajime', $result->fetch()->name);
    }
}
