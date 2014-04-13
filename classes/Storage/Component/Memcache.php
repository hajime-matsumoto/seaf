<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Storage\Component;

use Seaf\Base;

class Memcache
{
    use Base\SeafAccessTrait;

    private $mem;

    /**
     * コンストラクタ
     */
    public function __construct ($cfg)
    {
        if (!class_exists('Memcache')) {
            throw new Exception\Exception("Memcacheモジュールがロードされていません。");
        }

        $mem = new \Memcache ( );
        foreach ($cfg['servers'] as $server) 
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

    public function has ($key, $table = 'default')
    {
        $stat = $this->stat($key,$table);
        if($stat === false) return false;
        if($stat !== null) return true;
        return false;
    }

    public function put ($key, $value, $status, $table = 'default')
    {
        $key = $this->getKey($key,$table);
        $this->mem->set($key, $value);
        $this->mem->set($key.'_stat', $status);
    }

    public function stat ($key, $table = 'default')
    {
        $key = $this->getKey($key,$table);
        return $this->mem->get($key.'_stat');
    }

    public function del ($key, $table = 'default')
    {
        $key = $this->getKey($key,$table);
        $this->mem->del($key);
        $this->mem->del($key.'_stat');
    }

    public function get ($key, &$stat = null, $table = 'default')
    {
        $key = $this->getKey($key,$table);
        $this->mem->get($key);
        $stat = $this->mem->get($key.'_stat');
        return $this->mem->get($key);
    }

    private function getKey($key, $table)
    {
        return $table.'_'.$key;
    }

}
