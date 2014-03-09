<?php
/**
 * 環境
 */
namespace Seaf\Environment\Helper;

use Seaf\Environment\Environment;

/**
 * 配列操作ヘルパ
 * =========================
 *
 * 使いかた
 * ------------------------
 * $env->array(&$array, <bool:参照か否か>);
 */
class ArrayHelper 
{

    private $array = array();
    private $default = null;

    private $env;

    public function __construct (Environment $env = null)
    {
        $this->env = $env;
    }

    public function getDefault ($key) {
        return $this->default;
    }

    public function get ($key, $default) {
        if (array_key_exists($key,$this->array)) {
            return $this->array[$key];
        }
        return $default;
    }


    public function _set ($key, $value =false) {
        if (is_array($key)) {
            foreach ($key as $k=>$v) $this->_set($k,$v);
        }else{
            $this->$key = $value;
        }
    }

    public function _setDefault ($default) {
        $this->default = $default;
    }

    public function __get ($key) {
        if (array_key_exists($key,$this->array)) {
            return $this->array[$key];
        }
        return $this->getDefault($key);
    }

    public function __set ($key, $value) {
        return $this->array[$key] = $value;
    }


    public function __invoke($array)
    {
        $ah =  new ArrayHelper( );
        $ah->_set($array);
        return $ah;
    }

    public function __call ($name, $params) {
        if (method_exists($this,"_".$name)) {
            call_user_func_array(array($this,"_".$name), $params);
            return $this;
        }
        throw new \Exception('Invalid Method '.$name);
    }
}
