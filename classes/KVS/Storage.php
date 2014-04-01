<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\KVS;

use Seaf;
use Seaf\Util\ArrayHelper;

class Storage
{
    public static function factory ($storage)
    {
        if (is_object($storage)) return $storage;

        $g = ArrayHelper::getClosure('get');
        $type = $g($storage,'type', 'fileSystem');

        // KVSを立ち上げる
        $kvs = Seaf::ReflectionClass(
            'Seaf\\KVS\\Storage\\'.ucfirst($type).'Storage'
        )->newInstance();
        $kvs->configure($storage, false, true, ['type']);
        $kvs->initStorage();

        return $kvs;
    }
}
