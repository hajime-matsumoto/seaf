<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

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

use Seaf\Exception\Exception;
use Seaf\Factory\Factory;
use Seaf\Util\DispatchHelper;

/**
 * Environmentクラス
 *
 * 全機能を保持するクラス
 *
 * - メソッドマッピング
 * - インスタンスコンテナ
 */
class Environment
{
    /**
     * @var object
     */
    protected $base;

    /**
     * コンポーネントコンテナ
     * @var object
     */
    private $compContainer;

    /**
     * エクステンション用のコンテナ
     * @var object
     */
    private $extensionContainer;

    /**
     * メソッドコンテナ
     * @var object
     */
    private $methodContainer;

    /**
     * イベントコンテナ
     * @var object
     */
    private $eventContainer;

    /**
     * コンストラクタ
     */
    public function __construct( $base )
    {
        $this->base = $base;
        $this->compContainer      = new ComponentContainer( );
        $this->extensionContainer = new ComponentContainer( );
        $this->methodContainer    = new Container( );
        $this->eventContainer     = new EventContainer( );
    }

    /**
     * コンポーネントを登録する
     *
     * @param string
     * @param mixed
     * @param mixed
     */
    public function register( $name, $context, $callback = null )
    {
        $this->compContainer->register( $name, $context, $callback );
    }

    /**
     * メソッドをバインドする
     *
     * @param array $list メソッドと関数名のマップ
     * @param object $target バインドするオブジェクト
     */
    public function bind( $list, $target = null )
    {
        array_walk( $list, function( $function, $method ) use ($target){

            if( is_object($target) )
            {
                $function = array($target, $function);
            }
            $this->mapMethod( $method, $function);
        });
    }

    /**
     * コンポーネントを取得する
     *
     * @param string $name
     * @return object
     */
    public function getComponent( $name )
    {
        $comp = $this->compContainer->getComponent( $name );
        return $comp;
    }

    /**
     * コンポーネントがあるか？
     *
     * @param string $name
     * @return bool
     */
    public function hasComponent( $name )
    {
        return $this->compContainer->hasComponent( $name );
    }


    /**
     * メソッドをマップする
     *
     * @param string $medhot
     * @param callback $function
     */
    public function mapMethod( $method, $function )
    {
        $this->methodContainer->store($method, $function);
    }

    /**
     * メソッドは存在するか？
     *
     * @param string $medhot
     * @return bool
     */
    public function hasMethod( $method )
    {
        return $this->methodContainer->has($method);
    }

    /**
     * メソッドを取得
     *
     * @param string $medhot
     * @return callback
     */
    public function getMethod( $method )
    {
        return $this->methodContainer->restore($method);
    }

    /**
     * イベントフックを追加
     *
     * @param string 
     * @param callback
     */
    public function addHook( $key, $function )
    {
        $this->eventContainer->addHook( $key, $function );
    }

    /**
     * エクステンションを追加
     *
     * @param string $name
     * @return string $extension
     */
    public function addExtension( $name, $extension )
    {
        $this->extensionContainer->register( $name, $extension, function($ext) use ($name){
            $prefix = $name;
            $ext->init($prefix, $this->base ); // ExtensionにEnvironmentを与えて初期化させる
        });
    }

    /**
     * エクステンションを実体化させる
     *
     * @param string $name
     * @return string $extension
     */
    public function useExtension( $name )
    {
        return $this->extensionContainer->getComponent( $name );
    }

    /**
     * 現在の状況をプリントする
     */
    public function report( )
    {
        $cc = $this->compContainer;
        $ec = $this->extensionContainer;
        $mc = $this->methodContainer;
        $evc = $this->eventContainer;

        printf("\n=== コンポーネント ===\n");
        $cc->report();

        printf("\n=== エクステンション === \n");
        $ec->report();

        printf("\n=== メソッド === \n");
        printf("\n登録されているメソッド\n");
        foreach( $mc->getRef() as $k => $v ) {
            $method = "無名関数";
            if(is_array($v)) {
                list($class,$method) = $v;
                $method = get_class($class)."::".$method;
            }
            printf("%s : %s\n", $k, $method);
        }
        printf("\n=== イベント === \n");
        $evc->report();

    }

    /**
     * Bseからしか呼び出されない
     */
    public function run( $name, &$params )
    {
        // イベントを呼び出す
        $trigger = array($this->eventContainer,'trigger');

        // イベント引数を作る
        $output = "";
        $triggerArgs = array(&$params, &$output);

        DispatchHelper::dispatch( $trigger, array( $name.'.before', $triggerArgs));

        // ディスパッチ
        $result = DispatchHelper::dispatch($this->getMethod($name), $params);

        $args = array(
            $name.'.after',
            array(&$params,&$output)
        );
        DispatchHelper::dispatch( $trigger, array( $name.'.after', $triggerArgs));

        return $result;
    }
}



class Environment2
{
    /**
     * Environment Name
     * @var string
     */
    private $envName;


    /**
     * Action Dispatcher
     * @var object
     */
    private $actionDispatcher;

    /**
     * maped method
     */
    private $methods=array();


    /**
     * Construct Environment
     */
    public function __construct( )
    {
        $this->componentContainer = new ComponentContainer( 
            new FactoryContainer(
                array(
                    'config'     => 'Seaf\Config\Config',
                    'fileLoader' => 'Seaf\Loader\FileSystemLoader'
                )
            )
        );

        $this->actionDispatcher = new Dispatcher( );

        $this->action('set','stop',function($body){
            exit($body);
        });
    }

    /**
     * Set Environment Name
     *
     * @param string 
     */
    public function setEnvironmentName( $env_name )
    {
        $this->envName = $env_name;
    }

    /**
     * Access Factory Container Function 
     *
     * @param string $action
     */
    public function factory( $action )
    {
        $args = func_get_args();
        return call_user_func_array(
            array(
                $this->componentContainer,
                'factory'
            ), $args
        );
    }

    /**
     * Access Component Container Function 
     *
     * @param string $action
     * @param string $name
     * @param array $params
     */
    public function component( $action, $name, $params = array() )
    {
        return call_user_func_array(
            array(
                $this->componentContainer,
                $action.'Component'
            ),
            array($name) + $params
        );
    }

    /**
     * Access Action Dispatcher  Function 
     *
     * @param string $action
     */
    public function action( $action )
    {
        $args = func_get_args();
        array_shift($args);

        return call_user_func_array(
            array(
                $this->actionDispatcher,
                $action
            ),
            $args
        );
    }

    /**
     * Access Action Dispatcher Filter Function 
     *
     * @param string $action
     */
    public function filter( $action )
    {
        $args = func_get_args();
        array_shift($args);
        return call_user_func_array(
            array(
                $this->actionDispatcher,
                $action.'Filter'
            ),
            $args
        );
    }

    /**
     * map medhot
     */
    public function map( $name, $func )
    {
        $this->methods[$name] = $func;
    }

    public function getMethod($name)
    {
        return ArrayHelper::get($this->methods, $name, false);
    }


    public function run( $name, &$params )
    {
        return DispatchHelper::dispatch(
            $this->env->getMethod( $name ), $params
        );
    }
}
