<?php
/**
 * DI
 */
namespace Seaf\DI;

use Seaf\Commander\Command;

/**
 * ヘルパマネージャー
 */
class HelperManager extends InstanceManager {

    const NAMESPACE_SEPARATOR = '\\';
    const HELPER_SUFFIX = 'Helper';

    /**
     * @var array
     */
    private $helpers = array();

    /**
     * @var array
     */
    private $nsList = array();

    private $methods = array();

    private $target  = null;

    /**
     * @var object
     */
    public function __construct ($target = null) {
        parent::__construct();
        $this->target = $target;

        if (is_object($target)) {
            $class = get_class($target);
            while (!empty($class) && class_exists($class)) {
                $target_ns = substr($class,0,strrpos($class,self::NAMESPACE_SEPARATOR));
                $this->addHelperNamespace($target_ns.self::NAMESPACE_SEPARATOR);

                $class = get_parent_class($class);
            }
        }

        $this->bind($this, array(
            'map' => 'map',
            'bind' => 'bind'
        ));
    }

    /**
     * @var string
     */
    public function addHelperNamespace ($namespace) {
        $this->nsList[] = $namespace;
    }

    /**
     * メソッドマッピング
     *
     * @param string
     */
    public function map($name, $function) {
        if (is_string($function) && !is_callable($function)) {
            $function = array($this->target,$function);
        }
        $this->methods[$name] = $function;
    }

    /**
     * メソッドマッピング
     *
     * @param string
     */
    public function bind( $class, $list) {

        // 遅延束縛の解決
        foreach ($list as $k => $v) {
            if (is_string($class) && !class_exists($class)) {
                $target = $this->target;
                $func = function( ) use ($class,$v,$target) {
                    $class = $target->$class();
                    array($class,$v);
                    return Command::invokeArgs(array($class,$v),func_get_args());
                };
                $this->map($k, $func);
            } else {
                $this->map($k, array($class,$v));
            }
        }
    }


    /**
     * @var string
     * @return bool
     */
    public function has ($alias) {
        if (parent::has($alias)) return true;

        $options = array( $this->target );

        foreach ($this->nsList as $ns) {
            $class_name_base = 
                $ns
                .'Helper'.self::NAMESPACE_SEPARATOR
                .ucfirst($alias);

            if (class_exists($class_name_base)) {
                $this->register($alias, $class_name_base, $options);
                return true;
            } elseif (class_exists($class_name_base.self::HELPER_SUFFIX)) {
                $this->register($alias, $class_name_base.self::HELPER_SUFFIX, $options);
                return true;
            }
        }

        return false;
    }


    /**
     * @var string
     * @return object
     */
    protected function newInstance ($alias) {
        $instance = parent::newInstance($alias);
        if (!is_callable($instance)) {
            throw Exception\NotHelper($instance);
        }
        return $instance;
    }

    /**
     * @var string
     * @return bool
     */
    public function isCallable($name) {
        if (array_key_exists($name, $this->methods)) return true;
        if ($this->has($name)) return true;
    }

    /**
     * @var string
     * @var array
     * @return mixed
     */
    public function call ($name, $params) {
        if ($this->has($name)) {
            $instance = $this->get($name);
            return Command::invokeArgs($instance, $params);
        }
        if (array_key_exists($name, $this->methods)) {
            $closure = $this->methods[$name];
            return Command::invokeArgs($closure, $params);
        }
    }
}
