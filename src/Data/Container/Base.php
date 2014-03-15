<?php
namespace Seaf\Data\Container;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;

/**
 * コンテナパターン
 */
class Base implements ContainerIF
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param string $name
     * @param mixed $default
     */
    public function get($name, $default = null) 
    {
        if (!$this->has($name)) return $default;
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name) {
        return isset($this->data[$name]);
    }

    /**
     * @param string|array $name
     * @param mixed $value
     * @return bool
     */
    public function set($name, $value = false) {
        if (is_array($name)) {
            foreach($name as $k=>$v){
                $this->set($k, $v);
            }
            return;
        }
        $this->_set($name, $value);
    }

    public function _set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function toArray( )
    {
        return $this->data;
    }
}
