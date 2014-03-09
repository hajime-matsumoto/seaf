<?php
namespace Seaf\Helper;

/**
 * ArrayHelper
 * ================================
 */
class ArrayHelper {

    private $array;

    public static function get ($array, $name, $default = null) 
    {
        return !is_array($array) || array_key_exists($name, $array) ? $array[$name]: $default;
    }

    public static function set (&$array, $name, $value)
    {
        $array[$name] = $value;
    }

    public static function push (&$array, $name, $value)
    {
        if( !isset($array[$name])) {
            $array[$name]= array();
        }
        if( isset($array[$name]) && !is_array($array) ) {
            $array[$name] = array($array[$name]);
        }
        array_push($array[$name], $value);
    }


    public function __construct(&$array)
    {
        $this->array = &$array;
    }

    public static function init (&$array) 
    {
        return new ArrayHelper($array);
    }

    public function __call($name, $params)
    {
        if (in_array($name, array('push'))) {
            self::$name($this->array, $name, $params[0]);
            return $this;
        }
        if (!isset($params[0])) { // Getter
            return self::get($this->array, $name, null);
        }else{ //Setter
            self::set($this->array, $name, $params[0]);
            return $this;
        }
    }

}
