<?php
namespace Seaf\Kernel\Module;

use Seaf\Kernel\Kernel;
/**
 * Globals
 */
class Globals implements ModuleIF
{
    use ModuleTrait;

    private $GLOBALS;

    public function initModule (Kernel $kernel)
    {
        $this->GLOBALS = $GLOBALS;
    }

    public function __invoke ( $name = null, $default = null )
    {
        if ($name == null) return $this;

        return $this->get($name, $default);
    }

    public function get ($name, $default = null)
    {
        if(isset($this->GLOBALS[$name])) {
            return $this->GLOBALS[$name];
        }
    }
    public function set ($name, $value)
    {
        $this->GLOBALS[$name] = $value;
    }
}
