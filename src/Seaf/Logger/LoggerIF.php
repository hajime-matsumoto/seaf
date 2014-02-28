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

namespace Seaf\Logger;

use Seaf\Collection\ArrayCollection;

/**
 * LoggerIF
 */
interface LoggerIF
{
    /**
     * ログ送出
     */
    public function post( $message, $level );

    /**
     * PHPエラーハンドラ‐
     */
    public function phpErrorHandler( $no, $msg, $file, $line, $context );

    /**
     * 致命的なエラー
     *
     * @param string $message
     * @param $v...
     */
    public function fatal( $message );

    /**
     * エラー
     *
     * @param string $message
     * @param $v...
     */
    public function err( $message );

    /**
     * 警告
     *
     * @param string $message
     * @param $v...
     */
    public function warn( $message );

    /**
     * インフォ
     *
     * @param string $message
     * @param $v...
     */
    public function info( $message );

    /**
     * デバッグ
     *
     * @param string $message
     * @param $v...
     */
    public function debug( $message );
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
