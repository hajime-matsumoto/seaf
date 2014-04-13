<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Storage\Component;

use Seaf\Base;

class FileSystem
{
    use Base\SeafAccessTrait;

    private $dir;


    /**
     *
     */
    public function __construct ($dir = '')
    {
        $this->dir = $dir;
    }

    public function has ($key, $table = 'default')
    {
        $dataFile = $this->getStorageFile($key, $table);
        return file_exists($dataFile);
    }

    public function put ($key, $value, $status, $table = 'default')
    {
        $dataFile = $this->getStorageFile($key, $table);
        $statFile = $this->getStatFile($key, $table);
        file_put_contents($statFile, serialize($status));
        file_put_contents($dataFile, serialize($value));
    }

    public function stat ($key, $table = 'default')
    {
        $statFile = $this->getStatFile($key, $table);
        return unserialize(file_get_contents($statFile));
    }

    public function del ($key, $table = 'default')
    {
        $dataFile = $this->getStorageFile($key, $table);
        $statFile = $this->getStatFile($key, $table);
        unlink($dataFile);
        unlink($statFile);
    }

    public function get ($key, &$stat = null, $table = 'default')
    {
        $dataFile = $this->getStorageFile($key, $table);
        $stat = $this->stat($key, $table);
        return unserialize(file_get_contents($dataFile));
    }

    private function getStorageDir()
    {
        if (empty($this->dir)) {
            return $this->sf()->Reg( )->get('var_path');
        }
        return $this->dir;
    }
    private function getStorageFile($key, $table)
    {
        return $this->getStorageDir().'/'.$table.'/'.sha1($key);
    }

    private function getStatFile($key, $table)
    {
        return $this->getStorageDir().'/'.$table.'/'.sha1($key).'_stat';
    }
}
