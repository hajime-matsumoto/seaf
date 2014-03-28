<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Kvs\Engine;

use Seaf;
use Seaf\Pattern;

/**
 * KVS Base Engine
 */
abstract class Base
{
    use Pattern\Configure;

    public function getConfigurePrefix ( )
    {
        return 'set';
    }

    public function __construct ($config = array())
    {
        $this->configure($config);
    }

    /**
     * キー指定で値を設定する
     *
     * @param string
     * @param mixed
     */
    public function set ($key, $value, $stat = array())
    {
        return $this->_set($key, $value, $stat);
    }
    abstract protected function _set ($key, $value, $stat);

    /**
     * キー指定で値を取得する
     *
     * @param string
     * @param null
     */
    public function get ($key, &$stat = null)
    {
        return $this->_get($key, $stat);
    }
    abstract protected function _get ($key, &$stat = null);

    /**
     * キー値を削除する
     *
     * @param string
     */
    public function del ($key)
    {
        return $this->_del($key);
    }
    abstract protected function _del ($key);

    /**
     * 全データを削除する
     */
    public function flush ( )
    {
        return $this->_flush( );
    }
    abstract protected function _flush ( );
}
