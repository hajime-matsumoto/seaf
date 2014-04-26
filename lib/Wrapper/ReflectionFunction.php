<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

/**
 * リフレクションクラス用のラッパ
 */
class ReflectionFunction extends \ReflectionFunction
{
    public static function factory($class)
    {
        $rfc = new self($class);
        return $rfc;
    }

    public static function create($class)
    {
        if (is_array($class)) {
            return ReflectionMethod::create($class[0], $class[1]);
        }
        $rfc = new self($class);
        return $rfc;
    }
}
