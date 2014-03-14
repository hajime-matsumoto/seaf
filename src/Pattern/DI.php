<?php
namespace Seaf\Pattern;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;
use Seaf\Data\Container;
use Seaf\Pattern\Factory;

abstract class DI {
    private $instances;
    private $factory;

    public function __construct ( )
    {
        $this->instances = new Container\Base();
        $this->factory = new Factory();
    }

    public function register ($name, $definition, $options = array(), $callback = null)
    {
        if (is_object($definition)) {
            $this->instances->set($name, $definition);
        } else {
            $this->factory->register($name, $definition, $options, $callback);
        }
    }

    public function has ($name)
    {
        if (!$this->instances->has($name)) {
            if (!$this->factory->has($name)) {
                return false;
            }
        }
        return true;
    }

    public function get ($name)
    {
        if (!$this->has($name)) {
            throw new Exception(array("%sは登録されていません",$name));
        }

        if ($this->instances->has($name)) {
            $instance =  $this->instances->get($name);
            return $instance;
        }
        $this->instances->set($name, $instance = $this->create($name));
        return $instance;
    }


    public function create($name)
    {
        return $this->factory->get($name)->create();
    }

    public function __call($name, $params)
    {
        return $this->call($name, $params);
    }

    public function call($name, $params)
    {
        if ($this->has($name)) {
            $instance = $this->get($name);
            if (is_callable($instance)) {
                return Kernel::dispatcher($instance, $params, $this)->dispatch();
            }
            return $instance;
        }
        return $this->DICallFallBack($name, $params);
    }

    abstract public function DICallFallBack($name, $params);
}
