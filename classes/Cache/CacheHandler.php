<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

class CacheHandler
{
    /**
     * キャッシュ経由でデータを取得する
     */
    public function cache ($key, $expire, $until, $callback, &$isHit = null)
    {
        if ($expire == 0) {
        }
        if ($this->has($key, $until)) {
            $isHit = true;
            return $this->getCachedData($key);
        } else {
            $isHit = false;
            return $this->put($key, $expire, $callback( ));
        }
    }

    /**
     * キャッシュストレージを設定する
     */
    public function setStorage($storage)
    {
        $this->storage = Storage::factory($storage);
    }


    /**
     * キャッシュストレージを取得する
     */
    public function getStorage( )
    {
        if (isset($this->storage)) {
            return $this->storage;
        }

        $this->setStorage([
            'type' => 'fileSystem',
            'path' => '/tmp/seaf.cache'
        ]);

        return $this->getStorage();
    }

    /**
     * キャッシュが存在するか
     *
     * @return bool
     */
    public function has ($key, $until = 0)
    {
        $result = $this->getStorage( )->has($key, $status);
        if ($result == false) return false;

        if ($until > 0 && $status['created'] < $until) {
            return false;
        }
        if ($status['expire'] > 0 && $status['expire'] < time()) {
            return false;
        }
        return true;
    }

    /**
     * キャッシュを作成する
     */
    public function put ($key, $expire, $data) 
    {
        $status['created'] = time();
        $status['expire'] = $expire;
        $this->getStorage( )->put($key, $data, $status);
        return $data;
    }

    /**
     * キャッシュを削除する
     */
    public function del ($key)
    {
        $this->getStorage( )->del($key);
        return $this;
    }

    /**
     * キャッシュを取得する
     */
    public function getCachedData ($key, &$status = null)
    {
        return $this->getStorage( )->get($key, $status);
    }
}
