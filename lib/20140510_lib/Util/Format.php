<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

/**
 * 文字列Utility
 */
class Format
{
    private $format;

    public function __construct($format)
    {
        $this->format = $format;
    }

    public function format( )
    {
        return $this->vformat(func_get_args());
    }
    public function vformat($args = [])
    {
        foreach($args as $k=>&$v){
            if (Util::isSchaller($v)) {
                continue;
            }
            $v = Util::dump($v, true, 1);
        }
        return vsprintf($this->format, $args);
    }

    public function __toString( )
    {
        return $this->format;
    }
}
