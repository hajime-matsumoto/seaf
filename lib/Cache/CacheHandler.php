<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

use Seaf\Data\KeyValueStore as KVS;
use Seaf\Base;
use Seaf\Registry;

class CacheHandler
{
    use Base\SingletonTrait;
    use KVS\KVSUserTrait;

    private $prefix;

    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * コンストラクタ
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * キャッシュセクションを取得する
     */
    public function section ($name)
    {
        $cache = new CacheHandler(empty($this->prefix) ? $name: $this->prefix.'.'.$name);
        $cache->setKVSHandler($this->getKVSHandler());
        return $cache;
    }

    /**
     * KVSテーブルを取得する
     *
     * @return KVS\Table
     */
    public function getKvsTable( )
    {
        return $this->getKvsHandler()->table('cache.'.$this->prefix);
    }


    /**
     * @param string
     * @param callable
     * @param int
     * @param int
     * @param ref
     * @return mixed
     */
    public function useCacheIfNotDebug(
        $key, $callback, $expires = 0, $until = 0, &$cacheStatus = null
    ){
        return $this->useCacheIf(
            !Registry\Registry::isDebug(),
            $key,
            $callback,
            $expires,
            $until,
            $cacheStatus
        );
    }
    /**
     * @param bool
     * @param string
     * @param callable
     * @param int
     * @param int
     * @param ref
     * @return mixed
     */
    public function useCacheIf(
        $bool, $key, $callback, $expires = 0, $until = 0, &$cacheStatus = null
    ){
        if ($bool == true) {
            return $this->useCache($key, $callback, $expires, $until, $cacheStatus);
        }
        $cacheStatus = false;
        return $callback($isSuccess);
    }

    /**
     * @param string
     * @param callable
     * @param int
     * @param int
     * @param ref
     * @return mixed
     */
    public function useCache(
        $key, $callback, $expires = 0, $until = 0, &$cacheStatus = null
    ){
        $table = $this->getKvsTable();

        $data = null;
        $hasCache = false;

        if ($table->has($key)) {
            $hasCache = true;

            $data = $table->get($key, $status);

            if ($status['expires'] > 0 && $status['expires'] < time()) {
                $hasCache = false;
            }

            if ($until > 0 && $status['created'] < $until) {
                $hasCache = false;
            }
        }

        if ($hasCache) {
            $cacheStatus = true;
            return $table->get($key);
        }

        $data = $callback($isSuccess);

        if ($isSuccess) {

            $table->set($key, $data, [
                'created' => time(),
                'expires' => $expires
            ]);
        }
        $cacheStatus = false;
        return $data;
    }
}
