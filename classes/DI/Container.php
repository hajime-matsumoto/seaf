<?php
namespace Seaf\DI;

use Seaf\Pattern;
use Seaf\Data;
use Seaf\Exception;

/**
 * DIコンテナ
 */
class Container extends Data\Container\ArrayContainer
{
    use Pattern\Factory;
    use Pattern\Event;

    private $owner;
    public $factory;

    /**
     * コンストラクタ
     */
    public function __construct ($owner = null)
    {
        $this->owner = $owner;
        $this->factory = new Factory( );
    }

    public function register($name, $definition, $opts = array(), $callback=null)
    {
        $name = ucfirst($name);

        if (is_object($definition) && !($definition instanceof \Closure)) {
            $this->set($name, $definition);
            return $this;
        }

        $this->factory->register($name, $definition, $opts, $callback);
        return $this;
    }


    /**
     * Hasをオーバライドする
     */
    public function has ($name)
    {
        $name = ucfirst($name);

        if (parent::has($name)) return true;
        if ($this->factory->has($name)) return true;

        return false;
    }

    /**
     * GETをオーバライドする
     */
    public function get ($name)
    {
        $name = ucfirst($name);

        if (parent::has($name)) return parent::get($name);

        if ($this->factory->has($name)) {
            $instance = $this->create($name);
            $this->set($name, $instance);
            return $instance;
        }

        return false;
    }

    /**
     * GetKeysをオーバライドする
     */
    public function getKeys ( )
    {
        return array_merge(
            parent::getKeys( ),
            $this->factory->getKeys( )
        );
    }


    /**
     * Createする
     */
    protected function create ($name)
    {
        $name = ucfirst($name);
        $instance = $this->factory->get($name)->create( );
        $this->trigger('create', $instance);
        return $instance;
    }

    /**
     * ファクトリを設定する
     */
    public function configFactory ($config)
    {
        $this->factory->configure($config);
    }

    /**
     * call
     */
    public function call ($name, $params)
    {
        $name = ucfirst($name);

        if ($this->has($name)) {
            $instance = $this->get($name);

            if (method_exists($instance, 'helper')) {
                return call_user_func_array(array($instance, 'helper'), $params);
            }
            return $instance;
        }

        throw new Exception\InvalidCall($name, $this);
    }
}
