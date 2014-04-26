<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

/**
 * リフレクションクラス用のラッパ
 */
class ReflectionMethod
{
    private $class;
    private $method;

    public function __construct ($class, $method)
    {
        $this->class = $class;
        $this->method = $method;
        $this->rfc = new \ReflectionMethod($class, $method);
    }

    public static function create($class, $method)
    {
        $rfc = new self($class, $method);
        return $rfc;
    }

    public function invokeArgs($params)
    {
        return $this->rfc->invokeArgs($this->class, $params);
    }
}
