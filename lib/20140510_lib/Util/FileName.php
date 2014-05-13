<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

use Seaf\Base;

/**
 * 文字列Utility
 */
class FileName extends Base\Container\ArrayContainer
{
    public function __construct($args)
    {
        parent::__construct($args);
        $parts = explode('/', $this->__toString());
        $this->data = $parts;
    }

    public function __toString( )
    {
        return (string) implode("/", $this->data);
    }

    public function exists( )
    {
        return file_exists($this->__toString());
    }
    public function mtime( )
    {
        return filemtime($this->__toString());
    }

    public function isDir()
    {
        return is_dir($this->__toString());
    }

    public function glob($pattern)
    {
        $data = $this->__toString().'/'.$pattern;
        return glob($data);
    }
}
