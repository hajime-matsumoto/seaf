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

    /**
     * 値が空値か
     *
     * @param string
     * @return bool
     */
    public function isEmpty($name);

    /**
     * ノーマルな配列に変換
     *
     * @param string
     * @return bool
     */
    public function toArray ($name  = null);

    /**
     * ダンプ
     *
     * @param string
     * @param int
     * @return bool
     */
    public function dump ($useReturn = false, $level = 5);

    /**
     * コンテナデータとして取得する
     */
    public function dict($name);

    /**
     * 値を配列で取得する
     */
    public function getArray($name);

    /**
     * 値を追加する
     */
    public function add($name, $value, $prepend = false);

    /**
     * 値を追加する
     */
    public function append($name, $value);

    /**
     * 値を追加する
     */
    public function prepend($name, $value);

    /**
     * 値をPOPする
     */
    public function pop($name);

    /**
     * 値をShiftする
     */
    public function shift($name);
}
