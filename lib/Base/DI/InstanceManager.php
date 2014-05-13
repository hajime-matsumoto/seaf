<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\DI;

/**
 * 
 */
use Seaf\Base\Event;
use Seaf\Util\Util;

/**
 * インスタンスマネージャ
 */
class InstanceManager implements Event\ObservableIF, InstanceManagerIF
{
    use Event\ObservableTrait;

    private $factory;
    private $container;
    private $owner;

    public function __construct($owner = null)
    {
        $this->owner = $owner !== null ? $owner: $this;
        $this->initInstanceContainer();
    }

    /**
     * コンストラクタ
     */
    public function initInstanceContainer ($owner = null)
    {
        // 大文字小文字の区別をしない
        $this->container = Util::Dictionary();
        $this->container->caseSensitive(false);
        $this->factory = new Factory\Factory();
    }

    public function getFactory( )
    {
        return $this->factory;
    }

    /**
     * インスタンスを取得する
     *
     * @param Factory\FactoryIF
     */
    public function getInstance($name)
    {
        if (func_num_args() > 1) {
            return $this->getInstanceArgs($name, array_slice(func_get_args(),1));
        }
        return $this->getInstanceArgs($name, []);
    }

    /**
     * インスタンスをセットする
     */
    public function setInstance($name, $instance)
    {
        $this->container->set($name, $instance);
    }

    /**
     * インスタンスがあるか
     */
    public function hasInstance($name)
    {
        if ($this->container->has($name)) return true;
        if ($this->getFactory()->canCreate($name)) return true;
        return false;
    }


    /**
     * インスタンスを取得する(実体)
     *
     * @param Factory\FactoryIF
     * @param array
     */
    public function getInstanceArgs($name, array $args = [])
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
        $instance = $this->newInstanceArgs($name, $args);
        $this->container->set($name, $instance);
        return $instance;
    }

    public function newInstance($name)
    {
        $args = func_num_args() > 1 ? 
            array_slice(func_get_args(),1):
            [];
        return $this->newInstanceArgs($name, $args);
    }

    public function newInstanceArgs($name, array $args = [])
    {
        $instance = $this->factory->newInstanceArgs($name, $args);

        if (!$instance)  {
            throw new \Exception(
                "!!! Instance Not Registered >>> $name <<< [".
                get_class($this->owner).
                "] !!!"
            );
        }

        $this->fireEvent('instance.create', [
            'name' => &$name,
            'args' => &$args,
            'instance' => $instance
        ]);

        return $instance;
    }

    public function register($name, $class=null, $args = [])
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) {
                if (is_array($v)) {
                    $class = $v[0];
                    $args  = isset($v[1]) ? $v[1]: [];
                }else{
                    $class = $v;
                    $args = [];
                }
                $this->register($k, $class, $args);
            }
            return $this;
        }

        if (is_object($class)) {
            $object = $class;
            $this->setInstance($name, $object);
            $this->fireEvent('instance.create', [
                'name' => $name,
                'args' => [],
                'instance' => $object
            ]);
            return $this;
        }

        $this->getFactory()->register(
            $class,
            $args,
            ['alias' => $name]
        );
        return $this;
    }
}
