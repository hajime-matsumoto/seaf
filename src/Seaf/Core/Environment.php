<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Environmentクラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;

use Seaf\Util\DispatchHelper;
use Seaf\Util\AnnotationHelper;
use Seaf\Exception\InvalidCall;
use Seaf\Exception\MethodAlreadyExists;

/**
 * Environmentクラス
 *
 * 1.メソッドを管理する
 * 2.依存性を管理する
 */
class Environment
{
    private $registory;
    private $methodContainer;
    private $eventContainer;
    private $DIContainer;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->initializeEnvironment();
    }

    /**
     * 初期化
     */
    public function initializeEnvironment( )
    {
        //= レジストリの設定
        $this->registory = new Container();

        //= メソッドの設定
        $this->methodContainer = new MethodContainer( );

        // 未定義の呼び出しをハンドルするメソッドを登録する
        $this->map('unDefinedCall',function( $name ){
            throw new InvalidCall("%sは未定義の呼び出しです。", $name);
        });

        //= DIコンテナの設定
        $this->DIContainer = new DIContainer( );

        //= エクステンションコンテナの設定
        $this->extensionContainer = new ExtensionContainer( );

        //= イベントコンテナの設定
        $this->eventContainer = new EventContainer( );

        //= オーバーライド可能なメソッドを定義する
        $logs = "";
        $this->setByRef('logs', $logs);
        $this->map('debug', function($message) use(&$logs) {
            $logs[] = $message;
        });
    }

    /**
     * レジストリセット
     */
    public function set( $name, $key )
    {
        $this->registory->store($name, $key);
    }

    /**
     * レジストリを参照でセットする
     */
    public function setByRef( $name, &$value )
    {
        $this->registory->store($name, $value);
    }

    /**
     * レジストリゲット
     */
    public function get( $name, $default = false )
    {
        if( $this->registory->has($name) )
        {
            return $this->registory->restore($name);
        }
        return $default;
    }

    /**
     * メソッドをマップする(上書き禁止)
     *
     * @param string $name
     * @param mixed $function
     */
    public function map( $name, $function )
    {
        if( $this->methodContainer->has($name) )
        {
            throw new MethodAlreadyExists("%sは既に定義されています。", $name);
        }
        $this->methodContainer->store( $name, $function);
    }

    /**
     * メソッドをマップする(上書きOK)
     *
     * @param string $name
     * @param mixed $function
     */
    public function remap( $name, $function )
    {
        $this->methodContainer->store( $name, $function);
    }

    /**
     * メソッドがマップされているか
     *
     * @param string $name
     * @return bool
     */
    public function isMaped( $name )
    {
        return $this->methodContainer->has( $name );
    }

    /**
     * メソッドを呼び出す(引数を配列でまとめる)
     *
     * @param string $name 呼び出すメソッドの名前
     * @param array $params 呼び出されるメソッドの引数リスト
     */
    public function callArgs( $name, $params )
    {
        array_unshift($params,$name);
        return DispatchHelper::invokeMethodArgs( $this, 'call', $params);
    }

    /**
     * メソッドを呼び出す
     *
     * @param string $name 呼び出すメソッドの名前
     * @param mixed $param,... 呼び出されるメソッドの引数リスト
     */
    public function call( $name )
    {
        if( $this->methodContainer->has( $name ) )
        {
            $method = $this->methodContainer->restore( $name );
        }
        else
        {
            return $this->call('unDefinedCall', $name, array_slice(func_get_args(),1));
        }
        return DispatchHelper::invokeArgs( $method, array_slice(func_get_args(),1) );
    }

    /**
     * DIパターン用のファクトリを登録する
     *
     * @param string $name
     * @param mixed $factory
     * @param mixed $callback
     */
    public function register( $name, $factory, $callback = null)
    {
        if( is_object($factory) )
        {
            $this->DIContainer->store( $name, $factory );
        }
        else
        {
            $this->DIContainer->addFactory( $name, $factory, $callback );
        }
    }

    /**
     * DIに登録されているか
     */
    public function isRegistered($name)
    {
        return $this->DIContainer->has($name);
    }

    /**
     * DIを取得する
     *
     * @param string $name
     * @return object
     */
    public function retrieve( $name )
    {
        return $this->DIContainer->retrieve( $name );
    }

    /**
     * エクステンションの追加
     *
     * @param string $prefix
     * @param mixed $factory
     * @param object $target
     */
    public function addExtension( $prefix, $factory )
    {
        $this->extensionContainer->addFactory( 
            $prefix,
            $factory,
            function($ext) use($prefix){
                $this->extend( $prefix, $ext );
            }
        );
    }

    /**
     * エクステンションの有効化
     */
    public function extend( $prefix, $ext )
    {
        $annotation = AnnotationHelper::get($ext);

        $classAnot  = $annotation->getClassAnnotation();

        // 初期化メソッドが指定されているか調べる
        if(isset($classAnot['SeafInitialize'])){
            $initializer = $classAnot['SeafInitialize'];
        }else{
            $initializer = "initializeExtension";
        }

        DispatchHelper::invokeMethodArgs(
            $ext,
            $initializer,
            array($prefix, $this)
        );

        $methodAnot = $annotation->getMethodAnnotation();
        foreach($methodAnot as $method=>$anot)
        {
            if( array_key_exists('SeafBind', $anot) )
            {
                $withOutPrefix = isset($anot['SeafBindPrefix']) && $anot['SeafBindPrefix'] == "false";
                if( !$withOutPrefix )
                {
                    $anot['SeafBind'] = $prefix.ucfirst($anot['SeafBind']);
                }
                $this->map($anot['SeafBind'], array($ext,$method));
            }
        }
    }

    /**
     * エクステンションの使用
     *
     * @param string $prefix
     */
    public function useExtension( $prefix )
    {
        $this->extensionContainer->retrieve( $prefix );
    }

    /**
     * イベントフックの登録
     *
     * @param type
     * @param callback
     */
    public function on( $type, $function )
    {
        $this->eventContainer->stack($type, $function);
    }

    /**
     * イベントフックの解除
     *
     * @param type
     * @param callback
     */
    public function off( $type, $function )
    {
        $this->eventContainer->remove($type, $function);
    }

    /**
     * イベントフックの呼び出し
     *
     * @param type
     * @param callback
     */
    public function trigger( $type )
    {
        if( !$this->eventContainer->has($type) )
        {
            return false;
        }

        foreach( $this->eventContainer->restore($type) as $function )
        {
            $continue = DispatchHelper::invokeArgs( $function, array_slice(func_get_args(),1));
            if( $continue === false ) break;
        }
    }


    /**
     * mapへのショートハンド
     *
     * @param array $list メソッドリスト
     * @param mixed $object メソッドを所有するオブジェクト
     */
    public function bind( $list, $object = null)
    {
        $self = $this;

        array_walk( $list, function( $v, $k) use ($object, $self){
            $isAssoc = is_string($k);
            if(!$isAssoc) $k = $v;

            if($object != null){
                if( is_string($object) ) $object = $this->retrieve($object);
                $v = array($object, $v);
            }
            $self->map( $k, $v);
        });
    }

    /**
     * ダンプする
     */
    public function dump()
    {
        return array(
            'registory' => $this->registory->toArray(),
            'methods'   => $this->methodContainer->toArray(),
            'DI'        => $this->DIContainer->toArray(),
            'Extension' => $this->extensionContainer->toArray()
        );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: */
