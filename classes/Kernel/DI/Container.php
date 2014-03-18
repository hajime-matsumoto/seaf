<?php
namespace Seaf\Kernel\DI;

use Seaf\DI;

class Container extends DI\Container
{
    /**
     * @var Container
     */
    private static $singleton;

    // ----------------------------------------------
    // スタティック
    // ----------------------------------------------
    public static function singleton ( )
    {
       return (self::$singleton) ? self::$singleton: self::$singleton = new self();
    }
    public static function who ( )
    {
        return __CLASS__;
    }
}
