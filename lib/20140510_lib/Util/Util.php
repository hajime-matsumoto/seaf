<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

use Seaf\Base;

/**
 * ユーティリティクラス
 */
class Util
{
    public static function dump ($data, $useReturn = false, $level = 5)
    {
        $dumper = new Dumper($data, $level);
        return $dumper->dump($useReturn);
    }

    public static function getReflectionClass ($class)
    {
        return new \ReflectionClass($class);
    }

    public static function ArrayContainer ($vars = [], $default = [])
    {
        if (empty($default)) $default = [];
        if (empty($vars)) $vars = [];
        if (!is_array($vars)) $vars = [$vars];
        $vars = array_merge($default, $vars);
        return new Base\Container\ArrayContainer($vars);
    }

    public static function String ($string = '')
    {
        return new String($string);
    }

    public static function ClassName ( )
    {
        return new ClassName(func_get_args());
    }

    public static function FileName ( )
    {
        return new FileName(func_get_args());
    }

    public static function Format ($format)
    {
        return new Format($format);
    }

    public static function isSchaller($v)
    {
        return !is_array($v) && !is_object($v) ?  true: false;
    }

    public static function man($object)
    {
        $man = new Man($object);
        $man->show();
    }
}
