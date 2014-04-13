<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class File
{
    private $basename;
    private $dirname;

    public function __construct ($name, $path = '')
    {
        if (!($this instanceof Dir)) {
            $dirname = dirname($name);
            if ($dirname != '.') {
                $path .= '/'.$dirname;
            }
        }
        $this->basename = basename($name);
        $this->dirname = $path;
    }

    public function getFileName( )
    {
        return $this->dirname.'/'.$this->basename;
    }

    public function isExists( )
    {
        return file_exists( $this->dirname.'/'.$this->basename );
    }

    public function __toString ( )
    {
        return $this->getFileName( );
    }

    public function ext ( )
    {
        return substr($this->basename, strrpos($this->basename, '.')+1);
    }

    public function basenameWithOutExt ( )
    {
        return substr($this->basename, 0, strrpos($this->basename, '.'));
    }

    public function mtime ( ) 
    {
        return filemtime($this->getFileName());
    }

    public function toArray ( )
    {
        if ($this->ext() == 'yaml') {
            return yaml_parse_file($this->getFileName());
        }
    }

    public function includeWithVars($vars)
    {
        $source = file_get_contents($this->getFileName());
        $compile = str_replace(
            ['{{','}}'],
            ['<?','?>'],$source);
    
        extract($vars);
        ob_start();
        eval('?>'.$compile);
        return ob_get_clean();
    }
}
