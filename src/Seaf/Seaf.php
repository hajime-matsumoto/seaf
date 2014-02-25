<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Seafのメインになるクラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf;

use Seaf\Core\Base;
use Seaf\util\DispatchHelper;


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
        return DispatchHelper::dispatch( array($seaf->base, $name), $params );
    }
}
