<?php
namespace Seaf\Data\Container;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;

/**
 * コンテナパターン
 */
abstract class ObjectiveContainer extends Base
{
    private $string;

    public function loadArray(array $data) 
    {
        foreach ($data as $k=>$v) {
            $this->set($k, $this->factory($v, $k));
        }
    }

    public function get ($name, $default = null)
    {
        if (false === $p = strpos($name, '.')) {
            if (parent::has($name)) {
                return parent::get($name);
            }
            return $this->getFallBack($name);
        }

        $current_name = substr($name, 0, $p);
        $next_name = substr($name, $p+1);

        // 見つからなかったときの処理
        if (!parent::has($current_name)) {
            return $this->getFallBack($name);
        }

        return parent::get($current_name)->get($next_name);
    }

    public function setString ($string)
    {
        $this->string = $string;
    }

    public function isArray ( )
    {
        return !empty($this->data);
    }

    public function isString ( )
    {
        return !empty($this->string);
    }

    public function toArray ()
    {
        $ret = array();
        foreach ($this->data as $k => $v) {

            $ret[$k] = $v->isArray() ? $v->toArray(): $v->toString();
        }
        return $ret;
    }

    public function toString ()
    {
        return $this->string;
    }

    public function isEmpty( )
    {
        return (empty($this->data) && empty($this->string));
    }

    abstract public function factory ($data, $key);
    abstract public function getFallBack ($key);
}
