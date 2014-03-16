<?php
namespace Seaf\Kernel\Module;

use Seaf\Kernel\Kernel;
use Seaf\Data;

/**
 * Globals
 */
class Globals implements ModuleIF
{
    use ModuleTrait;

    private $GLOBALS;

    private $list = array(
        'SERVER'  => '_SERVER',
        'REQUEST' => '_REQUEST',
        'POST'    => '_POST',
        'SESSION' => '_SESSION',
        'COOKIE'  => '_COOKIE',
        'FILES'   => '_FILES',
        'GET'     => '_GET',
        'argv'    => 'argv',
        'argc'    => 'argc'
    );

    public function initModule (Kernel $kernel)
    {
        foreach ($this->list as $k=>$v) {
            $this->GLOBALS[$k] = isset($GLOBALS[$v]) ? $GLOBALS[$v]: array();
        }
    }

    public function __invoke ( $name = null, $default = null )
    {
        if ($name == null) return $this;

        return $this->get($name, $default);
    }

    public function get ($name, $default = null)
    {
        if ($name == 'SESSION') {
            if (empty($this->GLOBALS['SESSION'])) {
                $this->GLOBALS['SESSION'] = $GLOBALS['_SESSION'];
            }
        }
        if(isset($this->GLOBALS[$name])) {
            return $this->GLOBALS[$name];
        }elseif(isset($this->GLOBALS["_".$name])) {
            return $this->GLOBALS["_".$name];
        }
    }
    public function set ($name, $value)
    {
        $this->GLOBALS[$name] = $value;
    }

    public function getHelper ()
    {
        return Data\Helper::factory($this->GLOBALS);
    }
}
