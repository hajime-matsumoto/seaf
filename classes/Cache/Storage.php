<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

use Seaf;
use Seaf\Util\ArrayHelper;

class Storage
{
    private $kvs;

    public static function factory ($storage)
    {
        if (is_object($storage)) return $storage;

        $g = ArrayHelper::getClosure('get');

        // KVSを立ち上げる
        $kvs = Seaf::ReflectionMethod(
            'Seaf\\KVS\\Storage', 'factory'
        )->invoke(null, $storage);

        $store = new self();
        $store->kvs = $kvs;

        return $store;
    }

    public function has($name, &$status)
    {
        return $this->kvs->has($name, $status);
    }

    public function put($key, $data, $status)
    {
        $this->kvs->put($key, $data, $status);
    }

    public function get($key, &$status)
    {
        return $this->kvs->get($key, $status);
    }

    public function del($key)
    {
        return $this->kvs->del($key);
    }
}
