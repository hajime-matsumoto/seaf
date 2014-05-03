<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

/**
 * コンテナIF
 */
interface ContainerIF
{
    /**
     * 値を格納する(単品)
     *
     * @param string
     * @param mixed
     */
    public function set($name, $value = null);

    /**
     * 値を取得する
     *
     * @param string
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * 値を削除する
     *
     * @param string
     */
    public function clear($name);

    /**
     * 値が存在するか
     *
     * @param string
     * @return bool
     */
    public function has($name);
}
