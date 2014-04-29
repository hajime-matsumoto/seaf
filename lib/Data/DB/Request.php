<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

class Request extends \Seaf\Com\Request\Request
{
    private $type;

    private $table_name;

    public static function factory ($type)
    {
        $req = new static( );
        $req->setType($type);
        return $req;
    }

    // --------------------------
    // チェインメソッド用
    // --------------------------
    
    public function tableName($name)
    {
        $this->setTableName($name);
        return $this;
    }

    // --------------------------
    // セッター
    // --------------------------
    public function setTableName($table)
    {
        $this->table_name = $table;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    // --------------------------
    // ゲッター
    // --------------------------
    public function getType( )
    {
        return $this->type;
    }

    public function getTableName( )
    {
        return $this->table_name;
    }

}
