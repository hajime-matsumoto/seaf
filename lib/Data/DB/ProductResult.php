<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Base;
use Seaf\Com;
use Seaf\Data;

class ProductResult extends Com\Result\Result
{
    protected $isError = false;
    protected $errMsg = false;

    public function isError ( )
    {
        return $this->isError;
    }
}
