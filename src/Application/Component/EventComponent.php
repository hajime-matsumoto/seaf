<?php
namespace Seaf\Application\Component;

use Seaf\Environment\Environment;
use Seaf\Kernel\Kernel;

/**
 * Event
 */
class EventComponent
{
    protected $events = array();

    private $env;

    public function initComponent(Environment $env)
    {
        $this->env = $env;
    }

    public function __invoke ($name)
    {
        Kernel::dispatch()->invokeArgs(array($this,'trigger'),func_get_args());
        return $this;
    }

    public function trigger($name)
    {
        if (isset($this->events[$name]) && is_array($this->events[$name]))
        foreach ($this->events[$name] as $handler) {
            if(!Kernel::dispatch()->invokeArgs($handler, array_slice(func_get_args(),1))) {
                break;
            }
        }
    }

    public function on($name, $handler, $append = true)
    {
        if (is_string($handler) && !is_callable($handler)) {
            $handler = array($this->env,$handler);
        }
        if ($append == true) {
            $this->events[$name][] = $handler;
        }else{
            if (!isset($this->events[$name])) $this->events[$name] = array();
            array_unshift($this->events[$name], $handler);
        }
        return $this;
    }

    public function off($name, $handler)
    {
        foreach($this->events[$name] as $k=>$v) {
            if ($handler == $v) {
                unset($this->events[$name][$k]);
            }
        }
    }

    public function clear($name)
    {
        $this->events[$name] = array();
    }

}

