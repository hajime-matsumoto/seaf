<?php
/**
 * データ操作:コンフィグ
 */
namespace Seaf\Data\Config;

use Seaf\Kernel\Kernel;
use Seaf\Data\Container\Base;

/**
 * コンフィグ用のコンテナ
 */
class Container extends Base
{
    private $sections = array();
    private $config;
    private $string;
    private $object;
    private $is_empty = false;

    public function __construct ($data, Config $config)
    {
        $this->config = $config;
        if (is_array($data)) {
            $this->set($data);
        }elseif ($data == null ) {
            $this->is_empty = true;
        }elseif (is_string($data) || is_int($data)){
            $this->string = $data;
        }elseif (is_object($data)){
            $this->object = $data;
        }
    }


    public function has($name)
    {
        if (false === ($p = strpos($name, '.'))) {
            return parent::has($name);
        }

        $newName = substr($name,0,$p);
        if (!$this->has($newName)) {
            return false;
        }
        return $this->get($newName)->has(substr($name,$p+1));
    }

    public function get($name, $value = null)
    {
        if (false === ($p = strpos($name, '.'))) {
            $data = parent::get($name, $value);
            if (empty($data)) {
                return new Container(null, $this->config);
            }
            return $data;
        }

        $newName = substr($name,0,$p);
        if (!$this->has($newName)) {
            return new Container(null, $this->config);
        }
        return $this->get($newName)->get(substr($name,$p+1));
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function _set($k, $v)
    {
        parent::_set($k, new self($v, $this->config));
    }

    public function toArray()
    {
        if (empty($this->data)) {
            return array();
        }
        $ret = array();

        foreach ($this->data as $k => $v)
        {
            $ret[$k] = $v->isString() ? $v->__toString(): $v->toArray();
        }
        return $ret;
    }

    public function isString ()
    {
        return isset($this->string);
    }

    public function filter($str)
    {
        $config = $this->config;
        return preg_replace_callback(
            '/\$(.+)\$/U', function ($m) use ($config) {
                if (defined($m[1])) return constant($m[1]);
                return (string) $config->get($m[1]);
            }, $str
        );
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        if (isset($this->object)) {
            return print_r($this->object,true);
        } elseif (!empty($this->data)) {
            return print_r($this->data, true);
        } else {
            return $this->filter($this->string);
        }
    }

    public function isEmpty()
    {
        return $this->is_empty;
    }


}
