<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Kvs\Engine;

use Seaf;
use Seaf\Exception;

/**
 * File Based KVS
 */
class FileEngine extends Base
{
    /**
     * Path
     *
     * @param string
     */
    private $path;
    private $dir;

    /**
     * Pathをセットする
     */
    public function setPath($path)
    {
        $dir = Seaf::FileSystem($path);

        if (!$dir->isDir()) $dir->mkdir();

        // Check
        if (!$dir->isDir() || !$dir->isWritable()) {
            throw new Exception\Exception(array(
                "%sは書き込みできないかディレクトリではありません",
                $dir
            ));
        }

        $this->path = $path;
        $this->dir = $dir;
    }

    /**
     * キー指定で値を設定する
     */
    protected function _set ($key, $value, $stat = array())
    {
        $this->statFile($key)->putContents(serialize(array_merge($stat,array(
            'key'     => $key,
            'created' => time()
        ))));

        $this->dataFile($key)->putContents(serialize($value));
    }

    /**
     * キー指定で値を取得する
     */
    protected function _get ($key, &$stat = null)
    {
        $stat = unserialize($this->statFile($key)->getContents());
        return unserialize($this->dataFile($key)->getContents());
    }

    /**
     * キー値を削除する
     */
    protected function _del ($key)
    {
        $this->statFile($key)->unlink();
        $this->dataFile($key)->unlink();
    }

    /**
     * 全データを削除する
     */
    protected function _flush ( )
    {
        foreach ($this->dir as $file) 
        {
            $file->unlink();
        }
    }

    /**
     * データファイルを取得する
     *
     * @param string $key
     * @return Seaf\Uti\FileSystem\File
     */
    private function dataFile($key)
    {
        $key = sha1($key);
        return $this->dir->get($key);
    }

    /**
     * ステータスファイルを取得する
     *
     * @param string $key
     * @return Seaf\Uti\FileSystem\File
     */
    private function statFile($key)
    {
        $key = sha1($key);
        return $this->dir->get($key.'.stat');
    }
}
