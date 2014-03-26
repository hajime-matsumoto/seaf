<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class FileSystem
{
    private $paths = array();

    public function __construct ($paths = array())
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    public function get ($file)
    {
        foreach ($this->paths as $path) {
            if(file_exists($path.'/'.$file)) {
                return Base::factory($path.'/'.$file);
            }
        }
        return false;
    }
}
