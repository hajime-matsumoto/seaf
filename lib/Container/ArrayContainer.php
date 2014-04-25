<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

/**
 * 配列コンテナ
 */
class ArrayContainer implements \ArrayAccess,\Iterator
{
    use ArrayContainerTrait;  // コンテナにする。

    public function __construct ($data = [])
    {
        // コンテナのイニシャルデータとして登録する
        $this->initContainerData ($data);
    }

    public function __invoke($key, $default = null)
    {
        return $this->getVar($key, $default);
    }
}
