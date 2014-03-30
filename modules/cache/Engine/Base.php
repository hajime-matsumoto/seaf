<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Cache\Engine;

use Seaf;
use Seaf\Pattern;
use Seaf\Module\Kvs;

/**
 * Cache Base Engine
 */
abstract class Base
{
    use Pattern\Configure;

    public function getConfigurePrefix ( )
    {
        return 'set';
    }

    public function __construct ($config)
    {
        $this->initEngine( );
        $this->configure($config);
    }

    public function flush ( )
    {
        $this->_flush();
    }

    public function has ($key)
    {
        return $this->_has($key);
    }

    public function set ($key, $value, $expire)
    {
        $this->_set($key, $value, $expire);
    }

    public function get ($key, &$stat = null)
    {
        return $this->_get($key, $stat);
    }
}
