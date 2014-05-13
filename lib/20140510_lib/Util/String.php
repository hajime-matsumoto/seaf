<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

/**
 * 文字列Utility
 */
class String
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function format( )
    {
        $args = func_get_args();
        foreach($args as $k=>&$v){
            if (Util::isSchaller($v)) {
                continue;
            }
            $v = Util::dump($v, true, 1);
        }
        return vsprintf($this->string, $args);
    }

    public function __toString( )
    {
        return $this->string;
    }

    public function dump( )
    {
        Util::dump($this);
    }

}
