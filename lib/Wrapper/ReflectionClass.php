<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

/**
 * リフレクションクラス用のラッパ
 */
class ReflectionClass extends \ReflectionClass
{
    public static function factory($class)
    {
        $rfc = new self($class);
        return $rfc;
    }
    public static function create($class)
    {
        $rfc = new self($class);
        return $rfc;
    }
}
