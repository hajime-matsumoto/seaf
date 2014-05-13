<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Cache\Strategy;

use Seaf\Cache;
use Seaf\Util\Util;


use Memcache as PHPMemcache;

/**
 * キャッシュストラテジー : MemCache
 */
class Memcache extends Cache\Strategy
{
    private $prefix;

    public function __construct ($cfg)
    {
        $cfg = Util::Dictionary($cfg, [
            'servers' => ['localhost:11211']
        ]);


        $mem = new PHPMemcache ( );

        foreach ($cfg->get('servers',[]) as $server) 
        {
            if (false === $p = strpos($server, ':')) {
                $mem->addServer($server, 11211);
            } else {
                $mem->addServer(
                    substr($server, 0, $p),
                    substr($server, $p+1)
                );
            }
        }

        $this->mem = $mem;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * キャッシュを作成する
     *
     * @param string
     * @param mixed
     * @param int
     */
    public function createCache ($key, $data, $expires = 0)
    {
        $key = $this->keyFormatter($key);
        $this->mem->set($key, $data);
        $this->mem->set($key.'_status', [
            'expires' => empty($expires) ? 0: time() + $expires,
            'created' => time()
        ]);
    }

    /**
     * キャッシュを取得する
     *
     * @param string
     * @param mixed
     * @param int
     */
    public function retriveCache ($key, $until = 0)
    {
        $key = $this->keyFormatter($key);
        $status = $this->mem->get($key.'_status');

        if (!$status) {
            return false;
        }
        if (
            ($until == 0 || $status['created'] > $until)
            &&
            ($status['expires'] == 0 || $status['expires'] > time())
        ) {
            return $this->mem->get($key);
        }

        $this->destroyCache($key, false);
    }

    /**
     * キャッシュを破棄する
     *
     * @param string
     */
    public function destroyCache ($key, $usePrefix = true)
    {
        if ($usePrefix) {
            $key = $this->keyFormatter($key);
        }
        $this->mem->delete($key);
        $this->mem->delete($key.'_status');
    }

    private function keyFormatter($key)
    {
        return (empty($this->prefix) ? $key: $this->prefix.'.'.$key);
    }
}
