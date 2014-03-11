<?php

namespace Seaf\Config;

class Config extends Container
{
    private $container;

    public function __construct( )
    {
    }

}

class Container
{
    private $data = array();

    public static function factory ($data) {
        if (is_array($data)) {
            return new Container($data);
        }else{
            return $data;
        }
    }

    public function __construct( $data = null )
    {
        if (is_array($data)) {
            $this->loadArray($data);
        }
    }

    public function loadArray($data) {
        foreach ($data as $k => $v) {
            $this->data[$k] = Container::factory($v);
        }
    }

    public function get ($name) 
    {
        $token = strtok($name, '.');
        $head = $this;
        do {
            $head = $head->$token;
        } while ($token = strtok('.'));
        return $head;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }else{
            return $this->data[$name] = new self();
        }
    }

    public function __set($name, $value)
    {
        $this->data[$name] = self::factory($value);
    }

    public function isEmpty() 
    {
        return empty($this->data);
    }

    public function toArray( )
    {
        $array = array();

        foreach ($this->data as $k=>$v)
        {
            $array[$k] = is_string($v) ? $v: $v->toArray();
        }
        return $array;
    }

}
