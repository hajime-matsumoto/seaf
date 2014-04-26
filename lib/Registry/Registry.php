<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Registry;

use Seaf\Base;
use Seaf\Container;

class Registry extends Container\ArrayContainer
{
    use Base\SingletonTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * @return bool
     */
    public static function isProduction ( )
    {
        if (static::getSingleton( )->getVar('env') === 'production') {
            return true;
        }
        return false;
    }
}
