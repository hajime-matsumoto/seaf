<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Pattern;

trait Factory {

    use Configure;

    public static function factory ($config = array())
    {
        $class = new static( );
        $class->configure($config);
        return $class;
    }
}
