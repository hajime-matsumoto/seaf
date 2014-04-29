<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\MongoDB;

use Seaf\Base;
use Seaf\Com;
use Seaf\Data;

class Result extends Com\Result\Result
{
    private $result;
    private $isError = false;
    private $errMsg = false;

    public function __construct ($result, $error = null)
    {
        $this->dbResult = $result;

        if (!empty($error['err'])) {
            $this->errMsg = $error['err'];
            $this->isError = true;
        }
    }

    public function isError ( )
    {
        return $this->isError;
    }
}
