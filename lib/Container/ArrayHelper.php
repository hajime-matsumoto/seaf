<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

/**
 * 配列操作ヘルパ
 */
class ArrayHelper
{
    /**
     * 値を取得する DOT区切り対応版
     *
     * @param array
     * @param stirng $name
     * @param mixed $default
     * @return mixed
     */
    public static function getWithDot($array, $name, $default = false)
    {
        $token = strtok($name, '.');
        $head = $array;
        do {
            if (!isset($head[$token])) {
                return $default;
            }
            $head = $head[$token];
        } while (false !== $token = strtok('.'));
        return $head;
    }

    /**
     * 値を取得する
     *
     * @param array
     * @param stirng $name
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, $name, $default = null)
    {
        // .区切りのアクセスを許可する
        if (strpos($name, '.')) {
            return self::getWithDot($array, $name, $default);
        }

        return isset($array[$name]) ? $array[$name]: $default;
    }

    /**
     * 値を設定する
     *
     * @param array
     * @param stirng $name
     * @param mixed $default
     * @return void
     */
    public static function set(&$array, $name, $value)
    {
        // .区切りのアクセスを許可する
        if (strpos($name, '.')) {
            return self::setWithDot($array, $name, $value);
        }
        $array[$name] = $value;
    }

    /**
     * 値を設定する DOT区切り対応版
     *
     * @param array
     * @param stirng $name
     * @param mixed $value
     * @return void
     */
    public static function setWithDot(&$array, $name, $value)
    {
        // DOT区切りを解決してポインタを取得
        $ref =& self::getRefWithDot($array, $name);

        // ポインタに値を代入
        $ref = $value;
    }

    /**
     * 参照を取得する
     */
    private static function &getRefWithDot(&$array, $name)
    {
        $token = strtok($name, '.');
        $head =& $array;
        do {
            if (!isset($head[$token])) {
                $head[$token] = [];
            }
            $head =& $head[$token];

        } while (false !== $token = strtok('.'));

        return $head;
    }

    /**
     * 値が存在すればTrue
     *
     * @param array
     * @param stirng $name
     * @return bool
     */
    public static function has ($array, $name)
    {
        // .区切りのアクセスを許可する
        if (strpos($name, '.')) {
            return self::getWithDot($array, $name, false);
        }

        return isset($array[$name]);
    }

    /**
     * コンテナを作る
     *
     * @param array
     * @param stirng $name
     * @return ArrayContainer
     */
    public static function container ($array)
    {
        return new ArrayContainer($array);
    }
}
