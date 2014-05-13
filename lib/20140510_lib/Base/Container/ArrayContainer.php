<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

/**
 * 配列コンテナ
 */
class ArrayContainer implements ContainerIF,\ArrayAccess,\Iterator
{
    use ArrayContainerTrait;

    /**
     * コンストラクタ
     */
    public function __construct ($array = [], $caceSensitive = true)
    {
        $this->set($array);
        $this->caceSensitive($caceSensitive);
    }

    /**
     * $this->get($name, $default)
     */
    public function __invoke($name, $default = null)
    {
        return $this->get($name, $default);
    }

    /**
     * $this->get($name, null)
     */
    public function __get($name)
    {
        $c = new ArrayContainer();
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = array();
        }
        if (!is_array($this->data[$name])) {
            return $this->data[$name];
        }
        $c->data =& $this->data[$name];
        return $c;
    }

    /**
     * $this->set($name, $value);
     */
    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function __toString( )
    {
        return (string) current($this->data);
    }
}
