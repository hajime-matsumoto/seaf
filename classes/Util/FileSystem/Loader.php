<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

use Seaf\Base;

class Loader
{
    use Base\RecurseCallTrait;

    private $paths;

    public function __construct($paths = [] )
    {
        $this->paths = $paths;
    }


    public function addPath ($path)
    {
        if ($this->recurseCallIfArray($path,__FUNCTION__,false)) return $this;
        $this->paths[] = $path;
    }

    public function getPaths( )
    {
        return $this->paths;
    }


    public function file ($name, $useReal = true)
    {
        if ($useReal == true) {
            foreach ($this->paths as $path) {
                if(file_exists($path.'/'.$name)) {
                    return new File($name, $path);
                }
            }
        }else{
            return new File($name, $this->paths[0]);
        }
    }

    public function dir ($name)
    {
        foreach ($this->paths as $path) {
            if(file_exists($path.'/'.$name) && is_dir($path.'/'.$name)) {
                return new Dir($name, $path);
            }
        }
        return new Dir($name, $this->paths[0]);
    }
}
