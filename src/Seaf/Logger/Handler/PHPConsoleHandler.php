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
class PHPConsoleHandler extends Handler
{
    private $console;
    private $handler;

    /**
     * スクリプト-開始
     */
    public function waikup( )
    {
        $this->console = \PhpConsole\Connector::getInstance();
        $this->handler = \PhpConsole\Handler::getInstance();
        $this->handler->start();
    }

    /**
     * ログ送出
     */
    protected function _post( $message )
    {
        $this->handler->debug( $message );
    }

    /**
     * スクリプト-終了
     */
    public function shutdown( )
    {
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
