<?php
namespace Seaf\Pattern;

use Seaf\Exception\Exception;
use Seaf\Data\Container;
use Seaf\Kernel\Kernel;

/**
 * ファクトリパターン
 */
class Factory
{
    private $definitions;

    public function __construct( )
    {
        $this->definitions = new Container\Base();
    }

    public function has ($name)
    {
        return $this->definitions->has($name);
    }

    public function get ($name)
    {
        return $this->definitions->get($name);
    }

    public function register ($name, $definition, $options, $callback)
    {
        $this->set(
            $name,
            $def = FactoryDefinition::factory($name, $definition, $options, $callback)
        );
        return $def;
    }

    public function set($name, FactoryDefinition $definition)
    {
        return $this->definitions->set($name, $definition);
    }
}

class FactoryDefinition 
{
    private $definition, $options = array(), $callback, $type;

    public static function factory ($name, $definition, $options, $callback)
    {
        if (empty($options)) $options = array();

        $def = new FactoryDefinition($definition, $options, $callback);

        if (is_string($definition) && class_exists($definition)) {
            $def->type = 'class';
        } elseif (is_callable($definition)) {
            $def->type = 'callback';
        } else {
            throw  new Exception(array("Definition Invalid %s %s",$name,print_r($definition,true)));
        }
        return $def;
    }

    public function __construct ($definition, $options, $callback)
    {
        $this->definition = $definition;
        $this->options = $options;
        $this->callback = $callback;
    }

    public function create( )
    {
        switch ($this->type) {
        case 'class':
            $instance = Kernel::ReflectionClass($this->definition)->newInstanceArgs($this->options);
            break;
        case 'callback':
            $instance = Kernel::dispatcher($this->definition)->dispatch($this->options);
            break;
        }
        if (is_callable($this->callback)) {
            $func = $this->callback;
            $func($instance);
        }
        return $instance;
    }
}
