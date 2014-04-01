<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\KVS\Storage;

use Seaf;
use Seaf\Pattern;

/**
 * KVS Storage Base
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
        if (!empty($config)) {
            $this->configure($config);
            $this->initStorage( );
        }
    }

    abstract public function initStorage ();

    /**
     * キー指定で値を設定する
     *
     * @param string
     * @param mixed
     */
    public function put ($key, $value, $stat = array())
    {
        return $this->_put($key, $value, $stat);
    }
    abstract protected function _put ($key, $value, $stat);

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
     * 値が存在するか
     *
     * @param string
     * @param null
     * @return bool
     */
    public function has ($key, &$stat = null)
    {
        return $this->_has($key, $stat);
    }
    abstract protected function _has ($key, &$stat = null);

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
