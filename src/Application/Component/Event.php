<?php
namespace Seaf\Application\Component;

use Seaf\Environment\Environment;
use Seaf\Kernel\Kernel;
use Seaf\Environment\Component\ComponentIF;

/**
 * Event
 */
class Event implements ComponentIF
{
    protected $events = array();

    private $env;

    public function initComponent(Environment $env)
    {
        $this->env = $env;
    }

    public function __invoke ($name = null)
    {
        if ($name == null) return $this;

        Kernel::dispatcher(array($this,'trigger'),func_get_args(), $this)->dispatch();
        return $this;
    }

    public function trigger($name)
    {
        if (isset($this->events[$name]) && is_array($this->events[$name]))
        foreach ($this->events[$name] as $handler) {
            if(!Kernel::dispatcher($handler, array_slice(func_get_args(),1), $this)->dispatch()) {
                break;
            }
        }
    }

    public function on($name, $handler, $append = true)
    {
        if (is_string($handler) && !is_callable($handler)) {
            $handler = array($this->env->application,$handler);
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

