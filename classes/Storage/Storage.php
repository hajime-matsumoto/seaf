<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Storage;

use Seaf\Base;

class Storage
{
    use Base\ComponentCompositeTrait;
    use Base\SeafAccessTrait;

    const DEFAULT_STORAGE_ENGINE = 'FileSystem';

    public function __construct ()
    {
        // コンポーネントコンテナを設定する
        $this->setComponentContainer('Seaf\Storage\ComponentContainer');
    }

    public function getHandler($name, $type)
    {
        return new StorageHandler($this, $name, $type);
    }

    /**
     * ストレージエンジンを取得する
     */
    protected function engine ($type)
    {
        if ($type == null) $type = self::DEFAULT_STORAGE_ENGINE;
        return $this->component($type);
    }

    public function has ($key, $table, $type)
    {
        return $this->engine($type)->has($key, $table);
    }

    public function put ($key, $value, $status, $table, $type)
    {
        return $this->engine($type)->put($key, $value, $status, $table);
    }

    public function stat ($key, $table, $type)
    {
        return $this->engine($type)->stat($key, $table);
    }

    public function get ($key, &$stat = null, $table, $type)
    {
        return $this->engine($type)->get($key, $stat, $table);
    }
}
