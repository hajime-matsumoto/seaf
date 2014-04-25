<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

/**
 * メソッドコンテナ
 */
class MethodContainer
{
    use MethodContainerTrait;  // メソッドコンテナにする。

    public function __invoke($name)
    {
        $params = func_get_args();
        $params = array_slice($params, 1);
        return $this->callMethodArray($name, $params);
    }
}
