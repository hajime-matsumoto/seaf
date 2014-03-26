<?php
namespace Seaf\Core\Component;

class ReflectionMethod
{
    public function __construct ( )
    {
    }

    public function helper ($class, $method)
    {
        if (is_object($class)) $class = get_class($class);

        if (!class_exists($class)) {
            Seaf::logger()->emerg(array(
                "クラス%sは定義されていません",
                $class
            ));
        }

        return new SeafReflectionMethod($class, $method);
    }
}

class SeafReflectionMethod extends \ReflectionMethod
{
}
