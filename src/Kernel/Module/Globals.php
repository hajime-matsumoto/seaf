<?php
namespace Seaf\Kernel\Module;

/**
 * Globals
 */
class Globals
{
    public function __invoke ( $name, $default = null )
    {
        return $this->get($name, $default);
    }

    public function get ($name, $default = null)
    {
        if(isset($GLOBALS[$name])) {
            return $GLOBALS[$name];
        }
    }
}
