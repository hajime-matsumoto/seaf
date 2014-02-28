<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Helper;

use Seaf\DI\DIContainer;
use Seaf\Collection\ArrayCollection;

/**
 * ヘルパーハンドリングクラス
 */
class HelperHandler
{
    /**
     * @var object
     */
    private $di;

    /**
     * @var object
     */
    private $map;

    /**
     * コレクション
     */
    public function __construct( )
    {
        $this->map = new ArrayCollection( );
    }

    /**
     * DIから生成された場合DIを保存する
     *
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        $this->di = $di;
    }

    /**
     * ヘルパをバインドする
     *
     * @param object $target
     * @param array $list
     */
    public function bind( $target, $list )
    {
        foreach( $list as $name=>$method )
        {
            $this->map->set($name, array($target, $method));
        }
    }


    /**
     * ヘルパをマップする
     *
     * @param string $name
     * @param mixed $func
     * @return bool
     */
    public function map( $name, $func )
    {
        return $this->map->set($name, $func);
    }

    /**
     * ヘルパがマップされているか調べる
     *
     * @param string $name
     * @return bool
     */
    public function isMaped( $name )
    {
        return $this->map->has($name);
    }

    /**
     * メソッドを実行する
     *
     * @param mixed $name
     * @param array $params
     * @return mixed
     */
    public function invokeArgs( $func, $params )
    {
        if( is_string($func) && $this->isMaped($func))
        {
            return call_user_func_array($this->map->get($func), $params);
        }
        return call_user_func_array($func, $params);
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
