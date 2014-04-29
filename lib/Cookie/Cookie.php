<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cookie;

use Seaf\Base;
use Seaf\Wrapper;

class Cookie
{
    use Base\SingletonTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function getParam ($name)
    {
        $g = Wrapper\SuperGlobalVars::getSingleton( );
        return $g->getVar('_COOKIE.'.$name);
    }
}
