<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * コレクションクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Collection;


use Seaf\Collection\Exception\Exception;


/**
 * コレクションクラス
 */
abstract class Collection
{
    /**
     * 値をセットする
     *
     * @param mixd $name
     * @param mixed $value
     */
    public function set( $name, $value = null )
    {
        if( is_array($name) ) 
        {
            foreach( $name as $k=>$v )
            {
                $this->set( $k, $v );
            }
            return;
        }
        return $this->_set($name, $value );
    }

    /**
     * 値を取得する
     *
     * @param string $name
     * @param mixed $default
     * @return mixed 
     */
    public function get( $name, $default = null )
    {
        if( $this->has($name) ) {
            return $this->_get($name);
        }
        return $default;
    }

    /**
     * 値をセットする
     *
     * @param string $name
     * @param mixed $value
     */
    abstract protected function _set( $name, $value );

    /**
     * 値を取得する
     *
     * @param string $name
     * @return mixed 
     */
 
    /**
     * 値の有無
     *
     * @param string $name
     * @return bool
     */
    abstract public function has( $name );
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/

