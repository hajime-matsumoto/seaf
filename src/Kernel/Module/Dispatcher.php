<?php
namespace Seaf\Kernel\Module;

use Seaf\Kernel\Kernel;
use Seaf\Exception\Exception;

/**
 * ディスパッチャー
 */
class Dispatcher extends Module
{
    /**
     * @var Kernel
     */
    private $kernel;


    public function initModule(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function __invoke ($callback, $params, $caller)
    {
        return $this->factory($callback, $params, $caller);
    }


    public function factory ($callback, $params, $caller)
    {
        if (is_array($callback) && is_callable($callback)) {

            list($class, $method) = $callback;
            return new DispatcherMethod($class, $method, $params, $caller);

        } elseif ($callback instanceof \Closure) {

            return new DispatcherClosure($callback, $params, $caller);

        } elseif (is_callable($callback)) {
            return new DispatcherInvoke($callback, $params, $caller);
        }

        throw new Exception(array("Undefind Dispatch %s",print_r($callback,1)));
    }
}

class DispatcherMethod
{
    private $class, $method, $caller, $params;

    public function __construct($class, $method, $params, $caller)
    {
        $this->class = $class;
        $this->method = $method;
        $this->caller = $caller;
        $this->params = $params;
    }

    public function dispatch( )
    {
        if (!is_object($this->class)) {
            if (class_exists($this->class)) {
               Kernel::logger()->emergency($this->class);
            }
        }
        if (!method_exists($this->class, $this->method)) 
        {
            if (is_callable($func = array($this->class, $this->method))) {
                return call_user_func_array($func, $this->params);
            }
        }
        $method = new \ReflectionMethod($this->class, $this->method);
        return $method->invokeArgs($this->class, $this->params);
    }
}

class DispatcherClosure
{
    private $callback, $params, $caller;

    public function __construct ($callback, $params, $caller)
    {
        $this->callback = $callback;
        $this->params = $params;
        $this->caller = $caller;
    }

    public function dispatch( )
    {
        //$method = new \ReflectionFunction($this->callback);
        //return $method->invokeArgs($this->params);
        return call_user_func_array($this->callback, $this->params);
    }
}

class DispatcherInvoke
{
    private $callback, $params, $caller;

    public function __construct ($callback, $params, $caller)
    {
        $this->callback = $callback;
        $this->params = $params;
        $this->caller = $caller;
    }

    public function dispatch( )
    {
        $callback = $this->callback;
        return call_user_func_array($this->callback, $this->params);
    }
}

