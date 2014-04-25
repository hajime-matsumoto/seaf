<?php // vim: set ft=php ts=4 sts=4 sw=4 et:


namespace Seaf\Factory;

use Seaf\Container\ArrayHelper;
use Seaf\Wrapper;

class ClassFactory
{
    private $className;

    public static function factory ($cfg) 
    {
        $Factory = new self();

        // 設定をコンテナに変換
        $cfg = ArrayHelper::useContainer($cfg);
        $Factory->setClassName($cfg('class_name'));

        return $Factory;
    }

    public function setClassName($class)
    {
        if (func_num_args( ) > 1) {
            $class = func_get_args();
        }

        if (is_array($class)) {
            foreach ($class as $n) {
                $class_name_parts[] = ucfirst($n);
            }
            $class = implode('\\', $class_name_parts);
        }

        $this->className = $class;
    }


    public function create ( )
    {
        return Wrapper\ReflectionClass::create(
            $this->className
        )->newInstanceArgs(func_get_args());
    }
}
