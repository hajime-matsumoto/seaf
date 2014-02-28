<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Factoryクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Factory;


use Seaf\Collection\ArrayCollection;
use Seaf\Factory\Factory;


/**
 * Factoryクラス
 */
class Factory extends ArrayCollection
{
    /**
     * コンストラクタ
     */
    public function __construct( )
    {
    }

    /**
     * ファクトリを登録
     *
     * @param string
     * @param mixed クラス名かコールバック
     */
    public function register( $name, $initializer, $callback = null)
    {
        $this->set( $name, compact('initializer','callback'));
    }

    /**
     * インスタンスを作成
     */
    public function create( $name )
    {
        if( !$this->has($name) )
        {
            throw new InitializerNotRegistered("%sは登録されていません。", $name);
        }

        $info = $this->get( $name );
        $init = $info['initializer'];
        $cb = $info['callback'];

        if( is_callable( $init ) )
        {
            $instance = $init();
        }
        elseif( is_string($init) )
        {
            $instance = new $init( );
        }

        if( is_callable( $cb ) )
        {
            $cb($instance);
        }

        return $instance;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/

