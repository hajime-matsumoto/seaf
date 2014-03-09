<?php
/**
 * DI
 */
namespace Seaf\DI;

use Seaf\Commander\Command;

/**
 * インスタンスマネージャー
 */
class InstanceManager {

    /**
     * @var array
     */
    private $instances = array();

    /**
     * @var Factory
     */
    private $factory = null;

    /**
     * @var array
     */
    private $callbacks = array();

    /**
     * <code>
     * new InstanceManager(new Factory() || null)
     * </code>
     *
     * @param Factory
     */
    public function __construct (Factory $factory = null) {

        if ($factory == null) $factory = new Factory();

        $this->factory = $factory;
    }

    /**
     * レジスター
     * ==================================
     *
     * @param string
     * @param Object|Closure|string
     * @param array
     * @param Closure
     */
    public function register ($alias, $context, $options = array(), $callback = null) {

        if (is_object($context) && !is_callable($context)) {
            $this->instances[$alias] = $context;
            return $this;
        }

        if($callback !== null && !is_callable($callback)) {
            throw new Exception\InvalidCallback($callback);
        }

        if ($callback !== null) {
            $this->callbacks[$alias] = $callback;
        }

        $this->factory->registerDefinition($alias, array(
            'definition' => $context,
            'options' => $options
        ));
        return $this;
    }

    /**
     * インスタンスが存在するか
     *
     * @param string
     */
    public function has ($alias) {
        if ($this->factory->hasDefinition($alias)) return true;
        return array_key_exists($alias, $this->instances);
    }

    /**
     * インスタンスの取り出し
     *
     * @param string
     * @param bool
     */
    public function get ($alias, $sub_alias = false) {

        $not_shared = !($sub_alias == false || empty($sub_alias));

        if ($not_shared == false && array_key_exists($alias, $this->instances)) {
            return $this->instances[$alias];
        }

        $instance = $this->newInstance($alias);

        if ($not_shared == false) {
            $this->instances[$alias] = $instance;
        }

        if (array_key_exists($alias, $this->callbacks)) {
            $return = Command::invokeArgs($this->callbacks[$alias], array($instance));
            if (is_object($return)) $instance = $return;
        }

        return $instance;
    }

    /**
     * インスタンスの作成
     */
    protected function newInstance ($alias) {
        $instance = $this->factory->create($alias);
        return $instance;
    }
}
