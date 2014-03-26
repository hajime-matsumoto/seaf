<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Data\Container;

/**
 * クッキー関するヘルパ
 */
class Cookie
{
    /**
     * クッキーをセットする
     *
     * @param string $name
     * @param string $value
     * @return string
     */
    public function set ($name, $value)
    {
        setcookie($name, $value);
        return $this;
    }

    /**
     * クッキーを削除する
     *
     * @param string
     */
    public function del ($name)
    {
        $this->setcookie($name, '', time() - 3600);
    }

    /**
     * クッキーを取得する
     *
     * @param string $name
     * @return string
     */
    public function get ($name)
    {
        return Seaf::Globals('_COOKIE.'.$name);
    }

    /**
     * クッキーが存在するか
     *
     * @param string $name
     * @return bool
     */
    public function has ($name)
    {
        return Seaf::Globals()->has('_COOKIE.'.$name);
    }

    protected function setcookie($name, $value, $expire = 0)
    {
        setcookie($name, $value, $expire, '/');
    }

}
