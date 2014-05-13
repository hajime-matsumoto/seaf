<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Cache;

use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Util\Util;

/**
 * 
 */
class CacheFacade extends Module\Facade
{
    private $cache_strategy;
    private $prefix = 'cache';

    public function __construct ($config = [])
    {
        $c = Util::ArrayContainer($config);
        $this->cache_strategy = Strategy::factory($c('strategy'));
        $this->cache_strategy->addObserver($this);

        $this->prefix = $c('prefix', $this->prefix);
    }

    /**
     * @See FacadeIF
     */
    public function execute (Command\RequestIF $request, $from = null)
    {
        $this->prefix = $request->dict('target')->implode('_');
        return parent::execute($request, $from);
    }


    /**
     * キャッシュを作る
     */
    public function createCache($key, $data, $expires = 0)
    {
        return $this->cache_strategy->createCache($this->prefix($key), $data, $expires);
    }

    /**
     * キャッシュを得る
     */
    public function retriveCache($key, $unless = 0)
    {
        return $this->cache_strategy->retriveCache($this->prefix($key), $unless);
    }

    /**
     * キャッシュを破棄
     */
    public function destroyCache($key)
    {
        return $this->cache_strategy->destroyCache($this->prefix($key));
    }

    /**
     * キャッシュを使う
     */
    public function useCache($key, $callback, $expires = 0, $until = 0)
    {
        if ($data = $this->retriveCache($key, $until)) {
            return $data;
        }

        $success = true;
        $data = $callback($success);

        if ($success == true) {
            $this->createCache($key, $data, $expires);
        }else{
            $this->warn(
                'CACHE_CALLBACK_FAILED', [
                    'Callback Function Returns False $key=%s',
                    $key
                ]
            );
        }

        return $data;
    }

    /**
     * プレフィックスを掛ける
     */
    private function prefix($key)
    {
        return $this->prefix .".".$key;
    }
}
