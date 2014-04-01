<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\KVS\Storage;

use Seaf;
use Seaf\Exception;
use Memcache;

/**
 * Memcache KVS
 */
class MemcacheStorage extends Base
{
    /**
     * @var \Memcache
     */
    private $mem;
    private $prefix = '';

    /**
     * Storageの初期化
     */
    public function initStorage ( )
    {
        if (!class_exists('Memcache')) {
            throw new Exception\Exception("Memcacheモジュールがロードされていません。");
        }

        $mem = new \Memcache ( );
        foreach ($this->servers as $server) 
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

    // - - - - - - - - - - - - - - - - - - - -
    // 設定
    // - - - - - - - - - - - - - - - - - - - -
    public function setServers($servers)
    {
        $this->servers = $servers;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    // - - - - - - - - - - - - - - - - - - - -
    // 操作
    // - - - - - - - - - - - - - - - - - - - -

    /**
     * キー指定で値を設定する
     */
    protected function _put ($key, $value, $stat = array())
    {
        $key = $this->prefix($key);
        $this->mem->set($key, $value);
        $this->mem->set($key.'.stat', $stat);
    }

    /**
     * キー指定で値を取得する
     */
    protected function _has ($key, &$stat = null)
    {
        $key = $this->prefix($key);
        $stat = $this->mem->get($key.'.stat');

        if($stat === false) return false;
        if ($stat !== null) return true;
        return false;
    }


    /**
     * キー指定で値を取得する
     */
    protected function _get ($key, &$stat = null)
    {
        $key = $this->prefix($key);
        $stat = $this->mem->get($key.'.stat');
        return $this->mem->get($key);
    }

    /**
     * キー値を削除する
     */
    protected function _del ($key)
    {
        $key = $this->prefix($key);
        $this->mem->delete($key.'.stat');
        return $this->mem->delete($key);
    }

    /**
     * 全データを削除する
     */
    protected function _flush ( )
    {
    }

    private function prefix ($key)
    {
        if (empty($this->prefix)) return $key;
        return $this->prefix.".".$key;
    }
}
