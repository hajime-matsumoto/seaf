<?php
namespace Seaf\Environment;

use Seaf\Kernel\Kernel;
use Seaf\Pattern\DI as Base;
use Seaf\Environment\Environment;
use Seaf\Environment\Component\ComponentIF;
use Seaf\Exception\Exception;

/**
 * Environment\DI
 */
class DI extends Base
{
    /**
     * オートロードするコンポーネントのネームスペース
     * @var array
     */
    public $component_ns_list;

    /**
     * @var Environment
     */
    private $env;

    /**
     *
     *
     * @param  Environment
     */
    public function __construct (Environment $env)
    {
        parent::__construct();

        $this->env = $env;

        // Componentをオートロードする
        $class = Kernel::ReflectionClass($env);
        do {
            $this->addComponentNamespace($class->getNamespaceName().'\\Component');
        } while ($class = $class->getParentClass());
    }

    /**
     * addComponentNamespace
     *
     * @param $class
     * @return void
     */
    private function addComponentNamespace ($ns)
    {
        $this->component_ns_list[$ns] = $ns;
    }

    /**
     * Pattern\DI::hasをオーバライドする
     *
     * @param $name
     * @return bool
     */
    public function has ($name)
    {
        if (parent::has($name)) return true;

        // コンポーネントから探す
        foreach ($this->component_ns_list as $ns) {
            $class = $ns.'\\'.ucfirst($name);
            if (class_exists($class)) { // 見つかったらDIに登録しておく
                $this->register($name, $class);
                return true;
            }
        }

        // カーネルのDIから探す
        if (Kernel::DI()->has($name)) {
            $this->register($name, Kernel::DI()->get($name));
            return true;
        }
        return false;
    }

    /**
     * Pattern\DI::createをオーバライドする
     *
     * @param $name
     * @return bool
     */
    public function create ($name)
    {
        $instance = parent::create($name);
        if ($instance instanceof ComponentIF) {
            $instance->initComponent($this->env);
        }
        return $instance;
    }



    /**
     * DICallFallBack
     *
     * @param string $name
     * @param array $params
     * @return void
     */
    public function DICallFallBack ($name, $parms)
    {
        throw new Exception(array(
            "DI %s を解決できません",
            $name
        ));
    }
}
