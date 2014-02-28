<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Logger\Handler;


/**
 * ログハンドラ
 */
class DisplayHandler extends Handler
{
    /**
     * スクリプト-開始
     */
    public function waikup( )
    {
    }

    /**
     * ログ送出
     */
    protected function _post( $message )
    {
        echo $message."\n";
    }

    /**
     * スクリプト-終了
     */
    public function shutdown( )
    {
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
