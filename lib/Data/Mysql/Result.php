<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\Mysql;

use Seaf\Base;
use Seaf\Data;
use Seaf\Registry\Registry;

/**
 * 結果
 */
class Result extends Data\DB\ProductResult
{
    private $dbResult;

    public function __construct ($result, $error = null)
    {
        $this->dbResult = $result;

        if (!empty($error)) {
            $this->errMsg = $error;
            $this->isError = true;
        }
    }
}

