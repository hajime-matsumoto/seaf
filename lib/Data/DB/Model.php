<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Data;
use Seaf\Annotation\AnnotationBuilder;

class Model extends Data\Model\Model
{
    private $isNewFlag = true;
    private $initialVars = [];

    /**
     * 遅延束縛
     */
    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * スキーマを取得する
     */
    public static function schema ( )
    {
        $schema = new Schema( );
        $anot = AnnotationBuilder::build(static::who());

        // テーブル名を取得
        $anot->mapClassHasAnot('SeafDataTableName', function ($value) use ($schema){
            $schema->tableName($value);
        });

        // フィールドを取得
        $anot->mapPropsHasAnot('SeafDataAttrs', function ($prop, $value) use($schema){
            parse_str($value, $params);
            $schema->field($prop, $params);
        });

        // インデックスを取得
        $anot->mapClassHasAnot('SeafDataIndex', function ($value) use($schema){
            parse_str($value, $params);
            $schema->index($params['name'], $params);
        });

        // プライマリキーを取得
        $anot->mapClassHasAnot('SeafDataPrimary', function ($value) use($schema){
            parse_str($value, $params);
            $schema->primary($params);
        });

        return $schema;
    }

    /**
     * テーブルを初期化する
     */
    public static function tableInitialize ( )
    {
        $schema = static::schema();
        $db = DBHandler::getSingleton();
        $table = $db->{$schema->tableName};
        $table->drop();
        $table->create($schema->toArray());
    }

    /**
     * モデルを作成する
     */
    public static function create ($datas)
    {
        $class = static::who();
        return new $class($datas, $isNew = true);
    }

    public function __construct ($datas = [], $isNewFlag = true)
    {
        $this->setVars($datas);
        $this->isNewFlag = $isNewFlag;
        if ($this->isNew() == false) {
            $this->initialVars = $this->getVars();
        }
    }

    public function setVars($datas)
    {
        foreach ($datas as $k=>$v) {
            $this->__set($k, $v);
        }
    }

    public function getVars ( )
    {
        $datas = [];
        foreach (static::schema()->fields as $k=>$V)
        {
            $datas[$k] = $this->__get($k);
        }
        return $datas;
    }

    public function getModifiedVars ( )
    {
        $datas = [];
        foreach (static::schema()->fields as $k=>$V)
        {
            if ($this->__get($k) !== $this->initialVars[$k]) {
                $datas[$k] = $this->__get($k);
            }
        }
        return $datas;
    }

    public function isNew()
    {
        return $this->isNewFlag;
    }

    public function save ( )
    {
        if ($this->isNew()) {
            $this->insert( );
        }else{
            $this->update( );
        }
    }

    public static function table( ) 
    {
        $class = static::who();
        return DBHandler::getSingleton( )->getTable(
            static::schema()->tableName
        )->setMethod('outputFilter', function ($rec) use ($class){
            return new $class($rec, false);
        });
    }

    public function insert ( )
    {
        static::table()->insert($this->getVars());
        $this->initialVars = $this->getVars();
        $this->isNewFlag = false;
    }

    public function update ( )
    {
        $pkey = static::schema( )->getPrimaryKey();

        static::table()->update(
            $this->getModifiedVars(),
            [
                $pkey => $this->__get($pkey)
            ]
        );
        $this->initialVars = $this->getVars();
    }

    public function __set ($name, $value)
    {
        if (static::schema()->hasField($name)) {
            if (method_exists($this, $method = 'set'.$name)) {
                call_user_func([$this,$method], $value);
            }else{
                $this->$name = $value;
            }
            return true;
        }

        if ($name == "_id") {
            $this->_id = $value;
            return true;
        }
        throw new Exception\CantSetField($name);
    }

    public function __get ($name)
    {
        if (static::schema()->hasField($name)) {
            if (method_exists($this, $method = 'get'.$name)) {
                return call_user_func([$this,$method], $name);
            }else{
                return $this->$name;
            }
            return true;
        }
        throw new Exception\CantGetField($name);
    }
}
