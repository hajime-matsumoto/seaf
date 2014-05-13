<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * バックエンドシステム
 */
namespace Seaf\BackEnd\Module;

use Seaf\Base\Proxy;
use Seaf\Util\Util;
use Seaf\Base\DI;
use Seaf\Base\Event;
use Seaf\Logging;
use Seaf\BackEnd\Module;

/**
 * モジュール仲介者
 */
class ModuleMediator implements ModuleFacadeIF,ModuleMediatorIF
{
    use ModuleFacadeTrait;

    private $instanceManager;
    private $mediators = [];
    private $sub_module_key = 'module';

    public function closestMediator()
    {
        return $this;
    }

    public function setSubModuleKey($name)
    {
        $this->sub_module_key = $name;
    }

    protected function initModuleMediator()
    {
        $this->instanceManager = new DI\InstanceManager($this);
        $this->instanceManager->addObserver($this);

        // インスタンスが作られた時の処理
        $this->instanceManager->on('instance.create', function($e) {
            $this->onInstanceCreate($e->name, $e->instance);
            $e->stop();
        });
    }

    public function onInstanceCreate($name, $instance) {
        $this->info("MEDIATOR",["Module Loaded >>>> %s as %s <<<<",$name,get_class($instance)]);
        $instance->addObserver($this);
        $instance->setParent($this);
        $instance->initFacade( );
    }

    // 子メディエータを作成する
    public function MediatorFactory($class)
    {
        $instance = util('className', $class)->newInstance();
        $this->onInstanceCreate($class, $instance);
        return $instance;
    }

    // 子メディエータを取得する
    public function Mediator($class)
    {
        if (!isset($this->mediators[$class])) {
            $this->mediators[$class] = $this->MediatorFactory($class);
        }
        return $this->mediators[$class];
    }


    /**
     * モジュールをロードする
     *
     * @param string
     */
    public function loadModuleFacade($name)
    {
        if (!$this->instanceManager->hasInstance($name)) {
            if ($this->hasParent()) {
                $this->debug('MEDIATOR',[
                    'Module DELEGATE >>> %s [%s to %s] <<<',
                    $name,
                    get_class($this),
                    get_class($this->getParent())
                ]);
                return $this->getParent( )->loadModuleFacade($name);
            }
        }

        try {
            return $this->instanceManager->getInstance($name);
        } catch (\Exception $e) {
            $this->crit(
                'MEDIATOR',[
                    'Faild to load module >>> %s <<< from %s %s',
                    $name,
                    get_class($this),
                    (string) $e
                ]
            );
        }
    }

    /**
     * モジュールを登録する
     *
     * @param string
     * @param string
     * @param array
     */
    public function registerModule($name, $class = null, $args = [])
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) {
                if (is_string($v)) {
                    $class = $v;
                    $args = [];
                }else{
                    $class = $v[0];
                    $args = isset($v[1]) ? $v[1]: [];
                }
                $this->registerModule($k, $class, $args);
            }
            return $this;
        }
        if (is_object($class)) {
            $this->instanceManager->register($name, $class);
            return $this;
        }
        $this->instanceManager->getFactory( )->register(
            $class, $args, $options = ['alias' => $name]
        );
        return $this;
    }

    /**
     * モジュールプロクシを起動
     */
    public function __get($name)
    {
        $request = new Module\ProxyRequest($this);
        return $request->$name;
    }

    /**
     * リクエストにモジュールがいなければ設定する
     * すでにモジュールがセットされていれば
     * モジュールに処理を委譲する
     *
     * @param Proxy\ProxyRequestIF
     * @param string
     * @return Proxy\ProxyRequestIF
     */
    public function __proxyRequestGet(Proxy\ProxyRequestIF $request, $name)
    {
        if ($request->hasParam($this->sub_module_key)) {
            $module = $request->getParam($this->sub_module_key);
            return $this->loadModuleFacade($module)->__proxyRequestGet($request, $name);
        }
        // Getされたらモジュール名をセット
        $next_request = clone $request;
        $next_request->setParam($this->sub_module_key, $name);
        return $next_request;
    }

    /**
     * リクエストにモジュールがいなければエラー
     * モジュールに処理を委譲する
     *
     * @param Proxy\ProxyRequestIF
     * @param string
     * @param array
     * @return Proxy\ProxyResultIF
     */
    public function __proxyRequestCall(Proxy\ProxyRequestIF $request, $name, $params)
    {
        if ($request->hasParam($this->sub_module_key)) {
            $module = $request->getParam($this->sub_module_key);
            $new_request = clone $request;
            $new_request->clearParam($this->sub_module_key);
            // モジュールをロードして
            // 処理を委譲する
            return $this->loadModuleFacade($module)->__proxyRequestCall(
                $new_request, $name, $params
            );
        }

        $this->crit("MEDIATOR", "モジュールが決定されていないリクエスト");
    }
}
