<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore\Component;

use Seaf\Container;
use Seaf\Data\KeyValueStore;
use Seaf\Base;

class Memcache implements KeyValueStore\KVSComponentIF
{

    private $mem;

    use Base\RaiseErrorTrait;

    /**
     * イニシャライズ
     */
    public function __construct($cfg)
    {
        $cfg = new Container\ArrayContainer($cfg);

        // エラーコードの準備
        $this->setErrorCode('MEMCACHE_MODULE_NOT_FOUND');

        if (!class_exists('Memcache')) {
            $this->riseError('MEMCACHE_MODULE_NOT_FOUND');
        }

        $mem = new \Memcache ( );

        foreach ($cfg('servers',['localhost']) as $server) 
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


    public function get ($table, $key, &$status = null) 
    {
        $status = $this->mem->get($table.'_'.$key.'_stat');
        return $this->mem->get($table.'_'.$key);
    }

    public function set ($table, $key, $value, $status = [])
    {
        $this->mem->set($table.'_'.$key, $value);
        $this->mem->set($table.'_'.$key.'_stat', $status);
    }

    public function has ($table, $key)
    {
        $stat = $this->status($table, $key);
        if($stat === false) return false;
        if($stat !== null) return true;
        return false;
    }

    public function status($table, $key)
    {
        return $this->mem->get($table.'_'.$key.'_stat');
    }

    public function del ($table, $key)
    {
        $this->mem->delete($table.'_'.$key);
        $this->mem->delete($table.'_'.$key.'_stat');
    }

}
