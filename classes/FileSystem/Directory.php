<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\FileSystem;

use Seaf\Exception\Exception;

class Directory extends File implements \Iterator
{
    private $dir;

    public function isDir( )
    {
        return true;
    }

    public function glob($pattern) 
    {
        $ret = array();
        foreach (glob($params, GLOB_BRACE) as $file)
        {
            $ret[] = Base::factory($file);
        }
        return $ret;
    }

    public function includeOnce ($path)
    {
        if (!Helper::exists($this->name.'/'.$path)) {
            throw new Exception (array(
                "%sは存在しません",
                $this->name.'/'.$path
            ));
        }
        include_once $this->name.'/'.$path;
    }

    // ----------------------------------
    // For Iterator
    // ----------------------------------
    
    public function current ( )
    {
        return Base::factory($this->current_file);
    }
    public function key ( )
    {
        return $this->current_key;
    }
    public function next ( )
    {
        $this->current_key++;
    }
    public function rewind ( )
    {
        $this->dir = dir($this->name);
        $this->current_key = 0;
    }
    public function valid ( )
    {
        $this->current_file = $this->dir->read();
        return $this->current_file;
    }

}
