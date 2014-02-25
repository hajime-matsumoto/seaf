<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラス定義ファイル
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;

use Seaf\Core\Exception\ContainerException;

/**
 * コンテナクラス
 */
class Container
{
    /**
     * データ保存用
     */
    private $contents = array();

    /**
     * 格納
     *
     * 第三引数が真なら配列として被った値を保持する
     *
     * @param string $name
     * @param mixed $value
     * @param bool 
     */
    public function store( $name, $value, $push = false )
    {
        if( !$this->has($name) || $push == false)
        {
            $this->contents[$name] = $value;
            return;
        }

        $this->push( $name,$value);
        return;
    }

    /**
     * プッシュで格納
     *
     * @param string $name
     * @param mixed $value
     */
    public function push( $name, $value )
    {
        // 配列にする
        if( !is_array($this->contents[$name]) )
        {
            $this->contents[$name] = array($this->contents[$name]);
        }

        array_push( $this->contents[$name], $value );
    }

    /**
     * キーが存在するか？
     *
     * @param string $name
     * @return bool
     */
    public function has( $name )
    {
        return isset( $this->contents[$name] );
    }

    /**
     * 取得
     *
     * @param string $name
     * @return bool
     */
    public function restore( $name )
    {
        if( !$this->has($name) )
        {
            throw new ContainerException(
                sprintf(
                    "%sは格納されていません。\n存在するキーは{%s}です。",
                    $name,
                    implode( ",", array_keys($this->contents))
                )
            );
        }

        return $this->contents[$name];
    }

    /**
     * 取得(配列)
     *
     * @param string $name
     * @return array 
     */
    public function restoreMulti( $name )
    {
        if( !$this->has($name) )
        {
            throw new ContainerException(
                sprintf(
                    "%sは格納されていません。\n存在するキーは{%s}です。",
                    $name,
                    implode( ",", array_keys($this->contents))
                )
            );
        }

        $data =  $this->contents[$name];
        if(!is_array($data)) {
            return array($data);
        }
        return $data;
    }

    /**
     * 参照でデータを渡す
     *
     * @return &array 参照
     */
    public function &getRef( )
    {
        return $this->contents;
    }

}
