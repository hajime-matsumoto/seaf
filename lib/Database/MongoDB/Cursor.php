<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\MongoDB;

use Seaf\Database;
use Seaf\Util\Util;

/**
 * MongoDB : Curor
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
        return $this->result->getNext();
    }

    public function fetchNum ( )
    {
        return $this->result->getNext();
    }
}
