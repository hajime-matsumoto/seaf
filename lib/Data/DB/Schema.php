<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

class Schema
{
    public $tableName;
    public $fields  = [];
    public $indexes = [];

    public function hasField($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * テーブル名を追加
     */
    public function tableName ($name)
    {
        $this->tableName = $name;
    }

    /**
     * フィールドを追加
     */
    public function field ($name, $attrs)
    {
        $this->fields[$name] = $attrs;
    }

    /**
     * インデックスを追加
     */
    public function index ($name, $attrs)
    {
        $this->indexes[$name] = $attrs;
    }

    /**
     * プライマリキーを設定
     */
    public function primary ($attrs)
    {
        $this->primary = $attrs;
    }

    public function toArray( )
    {
        return [
            'fields' => $this->fields,
            'indexes' => $this->indexes,
            'primary' => $this->primary
        ];
    }

    public function getPrimaryKey ( )
    {
        return $this->primary['name'];
    }
}
