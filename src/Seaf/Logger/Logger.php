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
 * Logger
 */
class Logger implements LoggerIF
{
    const LOG_FATAL = 1;
    const LOG_ERR   = 2;
    const LOG_WARN  = 4;
    const LOG_INFO  = 8;
    const LOG_DEBUG = 16;
    const LOG_ALL   = 31;

    public static $error_map = array(
        self::LOG_FATAL => 'FATAL',
        self::LOG_ERR   => 'ERR',
        self::LOG_WARN  => 'WARN',
        self::LOG_INFO  => 'INFO',
        self::LOG_DEBUG => 'DEBUG'
    );

    public static $php_error_map = array(
        E_COMPILE_ERROR     => self::LOG_FATAL,
        E_ERROR             => self::LOG_FATAL,
        E_PARSE             => self::LOG_FATAL,
        E_CORE_ERROR        => self::LOG_FATAL,
        E_RECOVERABLE_ERROR => self::LOG_ERR,
        E_USER_ERROR        => self::LOG_ERR,
        E_WARNING           => self::LOG_ERR,
        E_CORE_WARNING      => self::LOG_ERR,
        E_COMPILE_WARNING   => self::LOG_ERR,
        E_USER_WARNING      => self::LOG_WARN,
        E_DEPRECATED        => self::LOG_INFO,
        E_USER_DEPRECATED   => self::LOG_INFO,
        E_NOTICE            => self::LOG_DEBUG,
        E_USER_NOTICE       => self::LOG_DEBUG,
        E_STRICT            => self::LOG_DEBUG
    );

    private $handlerCollection;
    private $name = "Logger";

    public function __construct( )
    {
        $this->handlerCollection = new LogHandlerCollection();
    }

    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * ハンドラの登録
     */
    public function addHandler( $handler )
    {
        if( !is_object($handler) )
        {
            $handler = $this->createHandler( $handler );
        }
        $handler->setName( $this->name );

        $this->handlerCollection->set( $handler );
        $handler->waikup();

        return $handler;
    }

    /**
     * ハンドラの作成
     */
    public function createHandler($arr)
    {
        $info = new ArrayCollection($arr);
        $type = $info->get('type','display');
        $class = __NAMESPACE__.'\\Handler\\'.ucfirst($type).'Handler';

        return new $class( $info );
    }

    /**
     * ログ送出
     */
    public function post( $message, $level )
    {
        foreach( $this->handlerCollection as $handler )
        {
            $handler->post( $message, $level );
        }
    }

    /**
     * PHP: Shutdown Function
     */
    public function shutdownFunction( )
    {
        foreach( $this->handlerCollection as $handler )
        {
            $handler->shutdown();
        }
    }

    /**
     * PHPエラーハンドラ‐
     */
    public function phpErrorHandler( $no, $msg, $file, $line, $context )
    {
        $level   = self::$php_error_map[$no];
        $name    = self::$error_map[$level];

        $message = 'PHP: '.$msg.' '.$file.' '.$line;

        $this->$name($message);
    }

    /**
     * 致命的なエラー
     *
     * @param string $message
     * @param $v...
     */
    public function fatal( $message )
    {
        $this->post(
            $this->makeMessage(func_get_args()),
            self::LOG_FATAL
        );
    }

    /**
     * エラー
     *
     * @param string $message
     * @param $v...
     */
    public function err( $message )
    {
        $this->post(
            $this->makeMessage(func_get_args()),
            self::LOG_ERR
        );
    }

    /**
     * 警告
     *
     * @param string $message
     * @param $v...
     */
    public function warn( $message )
    {
        $this->post(
            $this->makeMessage(func_get_args()),
            self::LOG_WARN
        );
    }

    /**
     * インフォ
     *
     * @param string $message
     * @param $v...
     */
    public function info( $message )
    {
        $this->post(
            $this->makeMessage(func_get_args()),
            self::LOG_INFO
        );
    }

    /**
     * デバッグ
     *
     * @param string $message
     * @param $v...
     */
    public function debug( $message )
    {
        $this->post(
            $this->makeMessage(func_get_args()),
            self::LOG_DEBUG
        );
    }

    /**
     *
     */
    protected function makeMessage( $params )
    {
        if( count($params) < 2 )
        {
            return $params[0];
        }

        return vsprintf( $params[0], array_slice($params,1) );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
