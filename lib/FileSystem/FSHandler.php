<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FileSystem;

class FSHandler
{
    private $rootPath;

    public function __construct ($root = '/tmp')
    {
        $this->rootPath = $root;
        if (!is_dir($root)) {
            mkdir($root, 0777, true);
        }
    }

    public function filePutContents ($path, $data)
    {
        $path = $this->makePath($path);
        $dirname = dirname($path);
        if(!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
        file_put_contents($path, $data);
    }

    public function fileGetContents ($path)
    {
        $path = $this->makePath($path);
        return file_get_contents($path);
    }

    public function fileExists ($path)
    {
        $path = $this->makePath($path);
        return file_exists($path);
    }

    public function unlink ($path)
    {
        $path = $this->makePath($path);
        return unlink($path);
    }

    private function makePath($path)
    {
        return $this->rootPath.'/'.$path;
    }
}
