<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Net\Session;

use Seaf\Data\Container;
use Seaf\Kernel\Kernel;

class Base extends Container\Base
{
    public function __construct ()
    {
        $this->initSession( );
    }

    protected function initSession ( )
    {
        session_start();
        $this->data =& $_SESSION;
    }

    public function __invoke($name)
    {
        if (!isset($this->data[$name])) $this->data[$name] = array();
        return new SessionContainer($this->data[$name]);
    }

    public function &__get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        return $this->data[$name] = $value;
    }
}

class SessionContainer extends Base
{
    public function __construct(&$data)
    {
        $this->data = &$data;
    }

    public function destroy ( )
    {
        $this->data = null;
    }

}
