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

namespace Seaf\Core\Factory;

/**
 * ファクトリクラスの抽象クラス
 */
abstract class Factory
{
    protected $context;
    protected $callback;

    /**
     * インスタンスを生成する
     */
    abstract protected function createInstance( );

    /**
     * @param mixed 
     */
    public function __construct( $context, $callback = null )
    {
        $this->context = $context;

        if( is_callable($callback) )
        {
            $this->callback = $callback;
        }
    }

    /**
     * インスタンスを生成する
     */
    public function create( )
    {
        $instance = $this->createInstance();

        if( is_callable($this->callback) ) 
        {
            call_user_func(
                $this->callback, $instance
            );
        }
        return $instance;
    }
}
