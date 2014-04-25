<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore\Component;

use Seaf\Container;
use Seaf\FileSystem\FSHandler;
use Seaf\Data\KeyValueStore;

class FileSystem implements KeyValueStore\KVSComponentIF
{

    private $fs;

    public function __construct($cfg)
    {
        $cfg = new Container\ArrayContainer($cfg);
        $this->setFileSystem($f = new FSHandler($cfg('rootPath', '/tmp/seaf')));
    }

    public function setFileSystem(FSHandler $fs)
    {
        $this->fs = $fs;
    }

    public function getFileSystem( )
    {
        return $this->fs;
    }

    /**
     * イニシャライズ
     */
    public function get ($table, $key, &$status = null) 
    {
        $status = unserialize($this->getFileSystem( )->fileGetContents($table.'_'.sha1($key).'_status'));
        return unserialize($this->getFileSystem( )->fileGetContents($table.'_'.sha1($key)));
    }

    public function set ($table, $key, $data, $status = [])
    {
        $status['key'] = sha1($key);
        $status['origin_key'] = $key;
        $status['created'] = time();

        $this->getFileSystem( )->filePutContents($table.'_'.sha1($key), serialize($data));
        $this->getFileSystem( )->filePutContents($table.'_'.sha1($key).'_status', serialize($status));
    }

    public function status($table, $key)
    {
        return unserialize($this->getFileSystem( )->fileGetContents($table.'_'.sha1($key).'_status'));
    }

    public function has ($table, $key)
    {
        return $this->getFileSystem( )->fileExists($table.'_'.sha1($key));
    }

    public function del ($table, $key)
    {
        return $this->getFileSystem( )->unlink($table.'_'.sha1($key));
    }
}
