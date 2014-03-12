<?php
namespace Seaf\Core\Container;

/**
 * コンテナ
 * -----------------------------------
 * 配列操作を簡易にする
 */
class Container
{
    protected $data = array();

    public function __construct($data = array())
    {
        $this->set($data);
    }

    public function set ($name, $value = false)
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) {
                $this->set($k, $v);
            }
            return $this;
        }
        $this->data[$name] = $value;
    }

    public function get ($name, $default = false) 
    {
        if (!$this->has($name)) return $default;
        return $this->_get($name);
    }

    public function _get($name)
    {
        return $this->data[$name];
    }

    public function has ($name)
    {
        return isset($this->data[$name]);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

}
