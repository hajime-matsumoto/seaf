<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

use Seaf\Data\KeyValueStore as KVS;

class CacheHandler
{
    private $kvs;
    private $key;

    public function __construct($key='default')
    {
        $this->key = $key;
    }

    public function getKvsTable( )
    {
        return $this->kvs;
    }

    public function setKvsTable(KVS\Table $kvs)
    {
        return $this->kvs = $kvs;
    }

    public function useCache($key, $callback, $expires = 0, $until = 0, &$cacheStatus = null)
    {
        $table = $this->getKvsTable();
        $key = $this->key.'_'.$key;

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
