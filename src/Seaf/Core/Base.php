<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * ベースクラス定義
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */
 
namespace Seaf\Core;

use Seaf\Exception\Exception;
use Seaf\Util\DispatchHelper;

/**
 * ベースクラス
 *
 * 機能を集合管理するEnvironmentへのアクセスを
 * コントロールするクラス。
 *
 * 全ての機能はこのクラスを通してアクセスされる。
 */
class Base 
{
    /**
     * Environment
     * @var object 
     */
    private $env;

    /**
     * @var bool
     */
    private $isInitialized = false;


    /**
     * コンストラクタ
     */
    public function __construct( )
    {
        $this->init( );
    }

    /**
     * 初期化処理
     */
    public function init(  )
    {
        //== Environmentクラスのインスタンスを生成する
        $this->env = new Environment( $this );

        //== Environmentクラスに必要なオブジェクトを登録する
        $this->env->register('config', 'Seaf\Config\Config');

        //== Environmentクラスに必要なメソッドを登録する 


        //== ビルトインのエクステンションを仕込む
        $this->env->addExtension('web','Seaf\Net\WebExtension');
        $this->env->addExtension('err','Seaf\Util\ErrorExtension');
        $this->env->addExtension('mail','Seaf\Mail\MailExtension');

        //== 環境にメソッドを追加する
        
        // コンフィグへのアクセス
        $this->env->bind(
            array(
                'get' => 'getConfig',
                'set' => 'setConfig'
            ), $this->env->getComponent('config')
        );

        // 環境へのアクセス設定
        $this->env->bind(
            array(
                'report'        => 'report' // 現在の状態を報告する
                ,'useExtension' => 'useExtension' // エクステンションを使用する
                ,'register'     => 'register'
                ,'mapMethod'    => 'mapMethod'
                ,'hasComponent' => 'hasComponent'
                ,'getComponent' => 'getComponent'
                ,'addHook'      => 'addHook'
            ), $this->env
        );

        // ベースへのアクセス設定
        $this->env->bind(
            array(
                'after' => function($method, $function){
                    $this->addHook( $method.'.after', $function );
                },
                'before' => function($method, $function){
                    $this->addHook( $method.'.before', $function );
                }
            )
        );
        // 間に合わせ処理
        $this->env->mapMethod('debug', function($log) {
            vprintf( $log, array_slice(func_get_args(),1));
        });
        $this->env->mapMethod('stop', function($body) {
            echo $body;
            // For PHP UNIT
            if( ob_get_length() == 0 ) ob_start();
        });

        // 自分を取得させるメソッド
        $this->env->mapMethod('getBase', function() {
            return $this;
        });

        /* 設定を登録する */
        $this->set('view.path', '{{app.root}}/views');
        $this->set('tmp.path', '{{app.root}}/tmp');
        $this->set('cache.path', '{{tmp.path}}/cache');

        $this->isInitialized = true;
    }

    /**
     * 動的なメソッドを取り扱う
     *
     * @param string $called_name
     * @param array $called_params
     */
    public function __call( $called_name, $called_params )
    {
        if( $this->env->hasMethod( $called_name ) )
        {
            // ディスパッチされる事を通知したい。
            return $this->env->run( $called_name, $called_params );
            return DispatchHelper::dispatch(
                $this->env->getMethod( $called_name ), $called_params
            );
        }

        throw new Exception(
            '%sは登録されていません。',
            $called_name);
        /*

        if( is_callable($this->env->getMethod( $called_name)) )
        {
            return call_user_func_array(
                $this->env->getMethod($called_name),
                $called_params
            );
        }

        if( is_callable($this->env->action('get', $called_name)) )
        {
            return $this->env->run( $called_name, $called_params );
        }

        $prefix = substr($called_name, 0, 3);
        if( in_array($prefix, array('get','set','new','del')) )
        {
            return $this->env->component(
                $prefix,
                lcfirst(substr($called_name, 3)),
                $called_params
            );
        }

        if( $this->env->component('has', $called_name) )
        {
            return $this->env->component('get', $called_name);
        }
         */

    }
}
