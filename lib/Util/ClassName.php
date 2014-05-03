<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

use Seaf\Base;

/**
 * 文字列Utility
 */
class ClassName extends Base\Container\ArrayContainer
{
    public function __construct($args)
    {
        parent::__construct($args);
    }

    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->set($k, $v);
            return $this;
        }

        if (false !== strpos($name, '\\')) {
            return parent::set(explode('\\', $name));
        }else{
            return parent::set($name, $value);
        }

    }

    public function newInstance ( )
    {
        return $this->newInstanceArgs(func_get_args());
    }

    public function newInstanceArgs ($args = [])
    {
        return Util::getReflectionClass($this->__toString())
            ->newInstanceArgs($args);
    }

    public function format( )
    {
        return new ClassName(
            (string) Util::Format($this->__toString())->vformat(func_get_args())
        );
    }


    public function __toString( )
    {
        array_walk($this->data, function(&$v) {
            $v = ucfirst($v);
        });
        return (string) implode("\\", $this->data);
    }
}
