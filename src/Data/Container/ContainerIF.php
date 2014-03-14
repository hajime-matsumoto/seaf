<?php
namespace Seaf\Data\Container;

/**
 * コンテナパターン
 */
interface ContainerIF
{
    /**
     * @param string $name
     * @param mixed $default
     */
    public function get($name, $default = null);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param string|array $name
     * @param mixed $value
     * @return bool
     */
    public function set($name, $value = false);
}
