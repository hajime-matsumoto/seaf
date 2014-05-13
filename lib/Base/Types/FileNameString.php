<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * 型ライブラリ
 */
namespace Seaf\Base\Types;

/**
 * ファイル名型
 */
class FileNameString extends SeparatedString
{
    private $data = [];
    private $ext;

    public function __construct($default = [])
    {
        parent::__construct('/', $default);
    }

    public function ext($ext)
    {
        $this->ext = $ext;
        return $this;
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

    public function filterParts($v)
    {
        return trim($v);
    }

    public function __toString( )
    {
        $string = parent::__toString( );
        if (!empty($this->ext)) {
            if(false !== $p = strrpos($string, '.')) {
                $string = substr($string, 0, $p);
            }
            $string.= '.'.$this->ext;
        }
        return $string;
    }

}
