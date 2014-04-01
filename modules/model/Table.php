<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Model;

use Seaf;
use Seaf\Pattern;;

/**
 * テーブルのベースクラス
 */
class Table
{
    /**
     * テーブルのモデルクラス
     */
    private $modelClass;

    public static function who ( ) 
    {
        return __CLASS__;
    }

    /**
     * コンストラクタ
     *
     * @param モデルのクラス名
     */
    public function __construct ($class_name = false)
    {
        if ($class_name != false) {
            if (class_exists($class_name)) {
                $this->modelClass = $class_name;
            } else {
                $this->modelClass = Seaf::ReflectionClass(static::who())
                    ->getNamespaceName().'\\'.$class_name;
            }
        }
    }

    /**
     * スキーマを取得する
     */
    public function getSchema ( )
    {
        return call_user_func(array($this->modelClass, 'getSchema'));
    }

    /**
     * モデルを作成する
     */
    public function create ( )
    {
        return call_user_func(array($this->modelClass, 'create'));
    }
}
