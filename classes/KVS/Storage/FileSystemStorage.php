<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\KVS\Storage;

use Seaf;
use Seaf\Exception;

/**
 * File Based KVS
 */
class FileSystemStorage extends Base
{
    /**
     * Path
     *
     * @param string
     */
    private $path;
    private $dir;
    public $use_sha1_for_key = true;

    public function initStorage () 
    {
        $dir = $this->dir;
        if (!$dir->isDir()) $dir->mkdir();

        // Check
        if (!$dir->isDir() || !$dir->isWritable()) {
            throw new Exception\Exception(array(
                "%sは書き込みできないかディレクトリではありません",
                $dir
            ));
        }
    }

    /**
     * Pathをセットする
     */
    public function setPath($path)
    {
        $dir = Seaf::FileSystem($path);

        $this->path = $path;
        $this->dir = $dir;
    }

    /**
     * キー指定で値を設定する
     */
    protected function _put ($key, $value, $stat = array())
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
    protected function _has ($key, &$stat = null)
    {
        if ($this->statFile($key)->isExists()) {
            $stat = unserialize($this->statFile($key)->getContents());
            return true;
        }else{
            return false;
        }
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
        if ($this->use_sha1_for_key) $key = sha1($key);
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
        if ($this->use_sha1_for_key) $key = sha1($key);
        return $this->dir->get($key.'.stat');
    }
}
