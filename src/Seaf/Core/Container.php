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
     * スタックインデックス
     */
    private $stack_idx = array();

    /**
     * 格納
     *
     * 第三引数が真なら配列として被った値を保持する
     *
     * @param string $name
     * @param mixed $value
     * @param bool 
     */
    public function store( $name, $value, $to_stack = false )
    {
        if( !$this->has($name) || $to_stack == false)
        {
            $this->contents[$name] = $value;
            return;
        }

        $this->stack( $name,$value);
        return;
    }

    /**
     * スタックする
     *
     * @param string $name
     * @param mixed $value
     */
    public function stack( $name, $value )
    {
        // スタックが開始されていれば追記
        if(isset($this->stack_idx[$name]))
        {
            $this->stack_idx[$name]++;
            array_push(
                $this->contents[$name], 
                $value 
            );
            return;
        }

        // スタックが開始されていなくて、もう値が存在する場合
        if( !empty($this->contents[$name]) )
        {
            $this->stack_idx[$name] = 2;
            $this->contents[$name] = array($this->contents[$name]);
        }
        else
        {
            $this->stack_idx[$name] = 1;
            $this->contents[$name] = array( );
        }
        $this->contents[$name][] = $value;
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
     * 参照でデータを渡す
     *
     * @return &array 参照
     */
    public function &getRef( )
    {
        return $this->contents;
    }

}
