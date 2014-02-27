<?php
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
use Seaf\Exception\UndefinedDependency;

/**
 * DIコンテナ
 */
class DIContainer extends Container
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
     * @param mixed $factory
     * @param callable 
     */
    public function addFactory( $name, $factory, $callback = null )
    {
        if( $this->factoryContainer->has($name) )
        {
            throw new FactoryAlreadyExists("%sはすでに定義されています。", $name );
        }
        $this->factoryContainer->store( $name, $factory, $callback );
    }

    /**
     * ファクトリも調べる
     *
     * @param string $name
     * @return bool
     */
    public function has( $name )
    {
        return parent::has($name) || $this->factoryContainer->has($name);
    }

    /**
     * コンポーネントの取得
     *
     * @param string $name
     * @return object
     */
    public function retrieve( $name )
    {
        // すでに所持していればそのインスタンスを返す
        if( parent::has($name) )
        {
            return parent::restore( $name );
        }

        // そうでなければ新しいインスタンスを作成
        $this->store( $name, $this->createInstance( $name ) );
        return $this->restore( $name );
    }

    /**
     * インスタンスの生成
     *
     * @param string $name
     * @return object
     */
    public function createInstance( $name )
    {
        // ファクトリを所持していなければエラー
        if( !$this->factoryContainer->has($name) )
        {
            throw new UndefinedDependency($name.'は定義されていない依存性です');
        }

        $factory = $this->factoryContainer->restore($name);
        return $factory->create();
    }

    public function toArray( )
    {
        return array(
            'factory'=>$this->factoryContainer->toArray(),
            'contain'=>parent::toArray()
        );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: */
