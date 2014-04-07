<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class FileLoader
{
    public $paths = array();

    public function __construct ($paths = array())
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    public function find($base, $extensions=[])
    {
        foreach ($extensions as $ext) {
            if($file = $this->get($base.".".$ext)) {
                return $file;
            }
        }
        return false;
    }

    public function addPath($path)
    {
        if (is_array($path)) {
            foreach ($path as $v) {
                $this->addPath($v);
            }
            return $this;
        }
        $this->paths[] = $path;
        return $this;
    }

    public function get ($file)
    {
        foreach ($this->paths as $path) {
            if(file_exists($path.'/'.$file)) {
                return Base::factory(realpath($path.'/'.$file));
            }
        }
        return false;
    }
}
