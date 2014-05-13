<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\Base\Proxy;

use Seaf\Util\Util;

/**
 * リザルト
 */
class ProxyResult implements ProxyResultIF
{
    private $error_flg = false;
    private $error_msg = '';
    private $returnValue = null;

    public function setError($message)
    {
        $this->error_flg = true;
        $this->error_msg = $message;
        return $this;
    }

    public function getError( )
    {
        return $this->error_msg;
    }

    public function isError( )
    {
        return $this->error_flg;
    }

    public function set($value)
    {
        $this->returnValue = $value;
        return $this;
    }

    public function retrive( )
    {
        return $this->returnValue;
    }
}
