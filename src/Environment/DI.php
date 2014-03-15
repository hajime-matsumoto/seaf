<?php
namespace Seaf\Environment;

use Seaf\Kernel\Kernel;
use Seaf\Pattern\DI as Base;
use Seaf\Exception\Exception;

/**
 * Environment\DI
 */
class DI extends Base
{
    /**
     * @var Environment
     */
    private $env;

    /**
     * @param  Environment
     */
    public function __construct (Environment $env)
    {
        parent::__construct();
        $this->env = $env;
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
        if ($instance instanceof Component\ComponentIF) {
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
