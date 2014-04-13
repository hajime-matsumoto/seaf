<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

use Seaf\Base;

class Cache
{
    use Base\SeafAccessTrait;
    use Base\StorageTrait;

    public $enable = true;
    private $storage = 'FileSystem';

    public function __construct ($cfg)
    {
        if (isset($cfg['storage'])) {
            $this->storage = $cfg['storage'];
        }
    }

    public function getHandler($name)
    {
        return new CacheHandler($name, $this);
    }

    public function storage ( ) 
    {
        return $this->getStorageHandler('cache', $this->storage);
    }

    public function has ($name, $unless)
    {
        if ($this->enable == false) return false;

        if ($this->storage()->has($name)) 
        {
            $stat = $this->storage()->stat($name);
            if ($unless > 0 && $stat['CreatedAt'] < $unless) {
                return false;
            }
            if ($stat['Expires']>0 && $stat['Expires'] < time()) {
                $this->storage()->del($name);
                return false;
            }


            return true;
        }
    }

    /**
     * キャッシュを作成する
     */
    public function create ($name, $data, $expires = 0)
    {
        $this->storage()->put($name, $data, [
            'Key' => $name,
            'CreatedAt'=>time(),
            'Expires'=>$expires
        ]);
        return $data;
    }

    /**
     * キャッシュを取得する
     */
    public function get ($name, &$stat = null)
    {
        return $this->storage()->get($name, $stat);
    }

}
