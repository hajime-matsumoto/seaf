<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

use Seaf\Container;
use Seaf\Factory;
use Seaf\Component;

class Table implements KVSHandlerIF
{
    private $tableName;
    private $kvs;

    public function __construct ($tableName, KVSComponentIF $kvs)
    {
        $this->tableName = $tableName;
        $this->kvs = $kvs;
    }

    public function get ($key, &$status = null) 
    {
        return $this->kvs->get($this->tableName, $key, $status);
    }

    public function set ($key, $data, $status = [])
    {
        return $this->kvs->set($this->tableName, $key, $data, $status);
    }

    public function status($key)
    {
        return $this->kvs->status($this->tableName, $key);
    }

    public function has ($key)
    {
        return $this->kvs->has($this->tableName, $key);
    }

    public function del ($key)
    {
        return $this->kvs->del($this->tableName, $key);
    }
}
