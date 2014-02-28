<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Seafのメインクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf;

use Seaf\Core\Base;


/**
 * Seafメインクラス
 *
 * スタティックな呼び出しだけ可能
 * 内部的にはSeaf\Baseインスタンスを持ち
 * __callStaticを使用して処理を委譲している
 */
class Seaf
{
    /**
     * リリース状態を判定するフラグ
     */
    const ENV_DEVELOPMENT='development';
    const ENV_PRODUCTION='production';

    /**
     * シングルトンインスタンス
     * @var object
     */
    static private $instance;

    /**
     * Seaf\Baseオブジェクト
     * @var object
     */
    private $base;

    /**
     * 外部から呼び出さない用にコンストラクタは
     * privateにする
     */
    private function __construct() 
    {
        $this->base = new Base();

        // ロガーコンポーネントを登録
        $this->base->register('logger','Seaf\Component\Logger', function($logger){
            $logger->setName('Seaf');
        });
        // 有効化
        $this->base->logger()->register();

        $this->base->di('registry')->set('name','Seaf');


        // ダンパ
        $this->base->register('dumper','Seaf\Component\Dumper');

        // デバッガ
        $this->base->register('debugger','Seaf\Component\Debugger');

        // HTTP
        $this->base->register('http','Seaf\Component\Http');
    }

    /**
     * シングルトンインスタンスを取得する
     *
     * @return object
     */
    static public function getInstance () 
    {
        if (self::$instance) return self::$instance;

        self::$instance = new Seaf();
        return self::$instance;
    }

    /**
     * それ以外の呼び出しはBaseクラスに委譲する。
     * 委譲の方法はディスパッチャが管理する。
     *
     * @param name  $name 
     * @param array  $params params
     * @return mixed
     */
    static public function __callStatic($name, array $params = array()) 
    {
        $seaf = self::getInstance();

        return call_user_func_array(
            array($seaf->base,$name),
            $params
        );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
