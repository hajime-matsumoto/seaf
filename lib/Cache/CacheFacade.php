<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * キャッシュモジュール
 */
namespace Seaf\Cache;

use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;
use Seaf\Base\Component;

/**
 * モジュールファサード
 */
class CacheFacade implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;
    use ConfigureTrait;
    use Component\ComponentContainerTrait;

    protected static $object_name = 'Cache';

    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $module = null, $configs = [])
    {
        // 設定
        $this->configure($configs,[
            'prefix' => 'cache',
            'strategy' => [
                'type' => 'memcache',
                'servers' => ['localhost:11211']
            ]
        ]);

        if ($module instanceof Module\ModuleIF) {
            $this->setParentModule($module);
        }

        $this->registerComponent('strategy', function ( ) {
            return Strategy::factory($this->configs( )->get('strategy'));
        });
    }

    /**
     * マジックメソッド
     */
    public function __call($name, $params)
    {
        if (method_exists($this, $name)) {
            return $this->makeRequest( )->__call($name, $params);
        }

        if ($this->hasComponent($name)) {
            return $this->loadComponent($name);
        }

        $this->crit(['Invalid Request %s', $name]);
    }

    /**
     * マジックメソッド
     */
    public function __get($name)
    {
        return $this->makeRequest($name);
    }

    /**
     * マジックメソッド
     */
    public function __clone( )
    {
        $this->configs = clone $this->configs;
    }

    protected function proxyRequestGet($req, $name)
    {
        $newReq = clone $req;
        $newReq->addParam('section', $name);
        return $newReq;
    }

    protected function selectProxyHandler($req, $name)
    {
        if ($req->isEmptyParam('section')) {
            return $this;
        }

        $prefix = Util::SeparatedString('.', [
            $this->configs()->prefix,$req->getParam('section')
        ]);

        $handler = clone $this;
        $handler->configs()->set('prefix', $prefix);

        return $handler;
    }

    /**
     * キャッシュキーのプレフィックス
     */
    protected function prefix($prefix)
    {
        return $this->configs()->prefix.".".$prefix;
    }

    /**
     * キャッシュ経由でデータを取得
     */
    protected function useCache($key, $callback, $expires=0, $until=0)
    {
        $this->debug([
            'UseCache key(%s) expires(%s) until(%s, %s)',
            $key, $expires,$until,time()-$until
        ]);

        $data = $this->strategy()->retriveCache($used_key = $this->prefix($key), $until);

        if($data) {
            $this->debug("Status >>> HIT $used_key <<<");
            return $data;
        }

        $success = true;
        $data = $callback($success, $used_key);

        if ($success) {
            $this->debug("Status >>> CREATED $used_key <<<");
            $this->strategy()->createCache($this->prefix($key), $data, $expires);
            return $data;
        }
        $this->warn("Status >>> FAILED $used_key <<<");
    }

}
