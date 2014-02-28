<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Seafの環境クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;


use Seaf\DI\DIContainer;
use Seaf\Helper\System;

use Seaf\Core\Exception\InvalidCall;


/**
 * 環境クラス
 */
class Environment
{
    /**
     * DIコンテナオブジェクト
     * @var object
     */
    private $di;

    /**
     * 環境オブジェクトを初期化
     */
    public function __construct( )
    {
        $this->di = new DIContainer();
        $factory = $this->di->factory();

        // ヘルパハンドラを登録
        $factory->register(
            'helperHandler',
            'Seaf\Helper\HelperHandler', 
            function($hp) use ($factory)
            {
                $hp->map('register',array($factory,'register'));
            }
        );

        // レジストリコンポーネントを登録
        $factory->register(
            'registry',
            'Seaf\Component\Registry',
            function($reg)
            {
                $reg->set('name', 'base');
            }
        );

        // システムコンポーネントを登録
        $factory->register('system','Seaf\Component\System');

        // ルータコンポーネント
        $factory->register('router','Seaf\Component\Router');

        // イベントコンポーネント
        $factory->register('event','Seaf\Component\Event');

        // メールコンポーネント
        $factory->register('mail','Seaf\Component\Mail');
    }

    /**
     * DIを取得
     *
     * @param string $name 指定しなければコンテナを返す
     * @return object
     */
    public function di( $name = false)
    {
        if( $name == false ) return $this->di;

        return $this->di->get( $name );
    }

    /**
     * ショートハンド用
     *
     * @param string $name
     * @param string $params
     */
    public function __call( $name, $params )
    {
        $helperHandler = $this->di->get('helperHandler');
        if( $helperHandler->isMaped($name) )
        {
            return $helperHandler->invokeArgs( $name, $params );
        }

        if( $this->di->has($name) )
        {
            array_unshift($params,$name);
            return $helperHandler->invokeArgs( array($this->di,'get'), $params);
        }

        throw new InvalidCall("%sは解決できない呼び出しです。",$name);
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
