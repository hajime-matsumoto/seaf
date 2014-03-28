<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * 
 */
class Result
{
    private $handler;
    private $result;

    public function __construct ($handler, $result)
    {
        $this->handler = $handler;
        $this->result = $result;
    }

    /**
     * 1件だけ取得する
     */
    public function single ( )
    {
        return $this->fetch( );
    }

    /**
     * 1件だけ取得する
     */
    public function fetch ( )
    {
        return $this->handler->fetch($this->result);
    }

}
