<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util;

use Seaf;
use Seaf\Data\Container\ArrayContainer;

/**
 * 配列操作ヘルパ
 */
class ArrayHelper
{
    /**
     * 値を取得する DOT区切り用
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
     * hoge.hugaのように.区切りで配列内配列にアクセスできる
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

        if (self::has($array, $name)) {
            $data = $array[$name];
        } else {
            $data = $default;
        }

        return $data;
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

    /**
     * Get用のヘルパ
     *
     * @param array
     * @param stirng $name
     * @return ArrayContainer
     */
    public static function getter ( )
    {
        return Seaf::ReflectionMethod(__CLASS__, 'get')->getClosure( );
    }
}
