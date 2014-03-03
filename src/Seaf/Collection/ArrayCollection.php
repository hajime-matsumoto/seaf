<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * 通常配列型のコレクションクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Collection;


use Seaf\Collection\Exception\Exception;
use IteratorAggregate;
use ArrayIterator;

/**
 * コレクションクラス
 */
class ArrayCollection extends Collection implements IteratorAggregate
{
    /**
     * @var array
     */
    private $data = array();

    public function __construct( $data = array() )
    {
        $this->data = $data;
    }

    /**
     * 値をコピーする
     *
     * @param string $name
     * @param mixed $value
     */
    public function sync( ArrayCollection $collection )
    {
        $this->data =& $collection->data;
    }

    /**
     * 値をプッシュする
     *
     * @param string $name
     * @param mixed $value
     */
    public function push( $name, $value )
    {
        $this->data[$name][] = $value;
    }

    /**
     * 値をセットする
     *
     * @param string $name
     * @param mixed $value
     */
    protected function _set( $name, $value )
    {
        $this->data[$name] = $value;
    }

    /**
     * 値を取得する
     *
     * @param string $name
     * @return mixed 
     */
    protected function _get( $name )
    {
        return $this->data[$name];
    }

    /**
     * 値の有無
     *
     * @param string $name
     * @return bool
     */
    public function has( $name )
    {
        return isset( $this->data[$name] ) ? true: false;
    }

    /**
     * イテレータの取得
     */
    public function getIterator( )
    {
        return new ArrayIterator( $this->data );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
