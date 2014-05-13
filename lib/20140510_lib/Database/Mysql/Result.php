<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Database\Mysql;

use Seaf\Database;

/**
 * 結果
 */
class Result implements Database\ResultIF
{
    private $result;

    public function __construct ($result)
    {
        $this->result = $result;
    }

    public function isError( )
    {
        return $this->result === false;
    }
}

