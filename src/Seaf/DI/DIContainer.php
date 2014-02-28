<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * DIコンテナクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\DI;


use Seaf\Collection\ArrayCollection;
use Seaf\Factory\Factory;

// 例外
use Seaf\DI\Exception\NotRegisteredComponent;


/**
 * DIコンテナクラス
 */
class DIContainer extends ArrayCollection
{
    const METHOD_ACCEPT_DI = 'acceptDIContainer';

    /**
     * @var object
     */
    private $factory;

    /**
     * コンストラクタ
     */
    public function __construct( )
    {
        $this->factory = new Factory( );
    }

    /**
     * ファクトリを取得
     */
    public function factory()
    {
        return $this->factory;
    }

    /**
     * コンポーネントが依存することを通知
     *
     */
    public function depends( $name )
    {
        if( !$this->has($name) ) 
        {
            throw new NotRegisteredComponent("%sは登録されてません。", $name);
        }
    }

    /**
     * コンポーネントが存在すれば真
     *
     * @param string
     * @return bool
     */
    public function has( $name )
    {
        return parent::has($name) || $this->factory()->has($name);
    }


    /**
     * コンポーネントを取得
     *
     * @param string
     * @return object
     */
    public function get( $name )
    {
        if( !$this->has($name) ) 
        {
            throw new NotRegisteredComponent("%sは登録されてません。", $name);
        }

        if( parent::has( $name ) ) return parent::get($name);

        $this->set( $name, $this->newInstance($name) );
        return $this->get( $name );
    }

    /**
     * コンポーネントを作成
     *
     * @param string
     * @return object
     */
    public function newInstance( $name )
    {
        $instance =  $this->factory()->create($name);
        if( method_exists($instance, self::METHOD_ACCEPT_DI) )
        {
            call_user_func(
                array($instance,self::METHOD_ACCEPT_DI), $this
            );
        }
        return $instance;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/

