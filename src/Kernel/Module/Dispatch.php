<?php
namespace Seaf\Kernel\Module;

/**
 * ディスパッチャー
 */
class Dispatch
{
    public function __construct ( )
    {
    }

    public function newInstanceArgs ($class, $args)
    {
        $class = new \ReflectionClass($class);
        return $class->newInstanceArgs($args);
    }

    public function invokeStaticMethod ($class, $method)
    {
        if (is_object($class)) $class = get_class($class);
        return call_user_func_array(
            array($class,$method),
            array_slice(func_get_args(),2)
        );
    }

    public function invokeArgs ($method, $args)
    {
        return call_user_func_array($method, $args);
    }

    public function __invoke ($callback, $params, $caller)
    {
        return call_user_func_array($callback, $params);
    }
}
