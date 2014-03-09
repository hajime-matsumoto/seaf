<?php
/**
 * DI
 */
namespace Seaf\DI;

/**
 * コンポーネントマネージャー
 */
class ComponentManager extends InstanceManager {

    private $target;

    const NAMESPACE_SEPARATOR = '\\';
    const COMPONENT_SUFFIX    = 'Component';

    /**
     * @var array
     */
    private $helpers = array();

    private $nsList = array();

    public function __construct ($target = null) {
        parent::__construct();

        $this->target = $target;

        if (is_object($target)) {
            $class = get_class($target);

            while (!empty($class) && class_exists($class)) {
                $target_ns = substr($class,0,strrpos($class,self::NAMESPACE_SEPARATOR));
                $this->addComponentNamespace($target_ns.self::NAMESPACE_SEPARATOR);

                $class = get_parent_class($class);
            }
        }
    }

    public function addComponentNamespace ($namespace) {
        $this->nsList[] = $namespace;
    }

    public function newInstance ($alias) {
        $instance = parent::newInstance($alias);
        return $instance;
    }

    public function has ($alias) {
        if (parent::has($alias)) return true;

        $parent = $this->target;
        $options = array($parent);

        foreach ($this->nsList as $ns) {
            $class_name_base = 
                $ns
                .'Component'.self::NAMESPACE_SEPARATOR
                .ucfirst($alias);

            if (class_exists($class_name_base)) {
                $this->register($alias, $class_name_base, $options);
                return true;
            } elseif (class_exists($class_name_base.self::COMPONENT_SUFFIX)) {
                $this->register($alias, $class_name_base.self::COMPONENT_SUFFIX, $options);
                return true;
            }
        }

        return false;
    }
}
