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
use Seaf\Core\Factory\FactoryContainer;

/**
 * コンポーネントコンテナ
 *
 * コンポーネントを保持するクラス
 */
class ComponentContainer extends Container
{
    /**
     * ファクトリコンテナ
     */
    private $factoryContainer;

    /**
     * ファクトリクラスを保持する
     */
    public function __construct( )
    {
        $this->factoryContainer = new FactoryContainer( );
    }

    /**
     * ファクトリの登録
     *
     * @param string $name
     * @param mixed $context クラス名かコールバック関数
     * @param callable 
     */
    public function register( $name, $context, $callback = null )
    {
        $this->factoryContainer->register( $name, $context, $callback );
    }

    /**
     * コンポーネントがあるか？
     *
     * @param string $name
     * @return bool
     */
    public function hasComponent( $name )
    {
        return $this->has($name) || $this->factoryContainer->has($name);
    }
    /**
     * コンポーネントの取得
     *
     * @param string $name
     * @return object
     */
    public function getComponent( $name )
    {
        // すでに所持していればそのインスタンスを返す
        if( $this->has($name) )
        {
            return $this->restore( $name );
        }

        $this->store( $name, $this->newComponent( $name ) );
        return $this->restore( $name );
    }

    /**
     * コンポーネントの生成
     *
     * @param string $name
     * @return object
     */
    public function newComponent( $name )
    {
        // ファクトリを所持していなければエラー
        if( !$this->factoryContainer->has($name) )
        {
            throw new ComponentException($name.'は定義されていないコンポーネントです');
        }

        $factory = $this->factoryContainer->restore($name);


        return $factory->create();
    }

    /**
     * 現在の状況をプリントする
     */
    public function report( )
    {
        $this->factoryContainer->report();

        if( count($this->getRef()) < 1 ) return;
        printf("\nインスタンス化されているオブジェクト\n");
        foreach($this->getRef() as $k=>$v){
            printf("%s : %s\n", $k, get_class($v));
        };
    }
}
