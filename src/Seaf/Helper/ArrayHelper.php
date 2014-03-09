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
        return (is_array($array) && array_key_exists($name, $array)) ? $array[$name]: $default;
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

    public static function getWithDot ($array, $name, $default = null) 
    {
        if ( false !== strpos($name,'.') ) {
            $token = strtok( $name, '.' );
            $data = $array;
            do {
                $data = self::get($data, $token, false);
                if ($data == false) return $default;
            } while ( $token = strtok('.') );

            return $data;

        }else{
            return self::get($array, $name, $default);
        }
    }

    public static function setWithDot (&$array, $name, $value)
    {
        if ( false === strpos($name,'.')) return self::set($array, $name, $value);

        $token = strtok($name, '.');
        $data =& $array;
        do {
            if (!isset($data[$token])) $data[$token] = array();
            $data =& $data[$token];
        } while ($token = strtok('.'));

        $data = $value;
    }


    public static function init (&$array) 
    {
        return new ArrayHelperObject($array);
    }


}

class ArrayHelperObject
{
    public function __construct(&$array)
    {
        $this->array = &$array;
    }

    public function get($name, $default =null)
    {
        return ArrayHelper::get($this->array, $name, $default);
    }

    public function set($name, $value)
    {
        ArrayHelper::set($this->array, $name, $value);
        return $this;
    }
    public function push($name, $value)
    {
        return ArrayHelper::push($this->array, $name, $value);
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

