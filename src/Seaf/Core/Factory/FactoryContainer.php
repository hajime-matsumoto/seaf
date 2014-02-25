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

use Seaf\Core\Container;

/**
 * ファクトリコンテナ
 *
 * ファクトリを保持するクラス
 */
class FactoryContainer extends Container
{
    /**
     * コンストラクタ
     */
    public function __construct( )
    {
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
        $this->store($name, compact('context','callback'));
    }

    /**
     * ファクトリの取得
     *
     * 親メソッドrestoreを上書きする
     *
     * @param $name
     * @return object
     */
    public function restore( $name )
    {
        $factory_info = parent::restore($name);

        // 格納されていた情報をFactoryオブジェクトにする
        $factory = self::createFactory(
            $factory_info['context'],
            $factory_info['callback']
        );

        return $factory;
    }

    /**
     * ファクトリの作成
     *
     * @param mixed $context クラス名かコールバック関数
     * @param callable 
     */
    static function createFactory( $context, $callback = null )
    {
        if( is_string( $context ) ) 
        {
            return new FactoryClassName( $context, $callback );
        }

        if( is_callable( $context ) )
        {
            return new FactoryCallback( $context, $callback );
        }

        throw new FactoryException("クラス名でもコールバック関数でもありません。");
    }

    /**
     * 現在の状況をプリントする
     */
    public function report( )
    {
        if( count($this->getRef()) < 1 ) return;

        printf("\n登録されているオブジェクト\n");
        foreach($this->getRef() as $k=>$v){
            $v = $v['context'];
            if( is_object($v) ) {
                $v = get_class($v);
            }elseif(is_callable($v)){
                $v = "匿名関数";
            }elseif(is_array($v)){
                $v = implode(",", $v);
            }
            printf(
                "%s : %s\n",
                $k,
                $v
            );
        };
    }
}
