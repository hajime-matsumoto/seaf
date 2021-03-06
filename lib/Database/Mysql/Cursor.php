<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\Mysql;

use Seaf\Database;
use Seaf\Util\Util;

/**
 * Mysql:Curor
 */
class Cursor extends Database\Cursor
{
    private $result;
    /**
     * コンストラクタ
     */
    public function __construct ($result)
    {
        $this->result = $result;
    }

    /**
     *
     */
    public function fetchAssoc ( )
    {
        return $this->result->fetch_assoc();
    }

    public function fetchNum ( )
    {
        return $this->result->fetch_array(MYSQL_NUM);
    }
}
