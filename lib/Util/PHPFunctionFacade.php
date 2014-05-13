<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util;

use Seaf\BackEnd;
use Seaf\Base\Proxy;
use Seaf\Base\Module;

class PHPFunctionFacade implements Module\ModuleFacadeIF 
{
    use Module\ModuleFacadeTrait;

    private $container;

    protected static $object_name = 'PHPFunction';

    public function __construct(Module\ModuleIF $module  = null)
    {
        if ($module) {
            $this->setParentModule($module);
        }

        $this->container = Util::Dictionary();

        $this->set('implode', function ($sep, $datas = []) {
            if (is_null($datas)) {
                return '';
            }
            return implode($sep, $datas);
        });

    }

    /**
     * メソッドをセットする
     *
     * @param string $method_name
     * @param callable $action
     */
    protected function set ($name, $action)
    {
        $this->container->set($name, $action);
    }

    public function __call($name, $params)
    {
        $this->debug(['Method %s', $name]);

        if ($this->container->has($name)) {
            $this->debug(['Use Wrapper >>> %s <<<', $name]);
            $act = $this->container->get($name);
            return call_user_func_array($act, $params);
        }

        if ($name == 'exit') {
            $this->info(['>>>> EXIT CALLED <<<<', $name]);
            exit($params[0]);
        }

        if (function_exists($name)) {
            $this->debug(['Use Native >>> %s <<<', $name]);
            return call_user_func_array($name, $params);
        }
        $this->crit(['No HIT >>> %s <<<', $name]);
    }
}
