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

namespace Seaf\Component;

use Seaf\Seaf;
use Seaf\DI\DIContainer;

/**
 * Systemコンポーネント
 */
class System
{
    /**
     * @var bool
     */
    private $fake_exit = false;

    /**
     * @var object
     */
    private $di;

    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        $this->di = $di;
    }

    /**
     * ヘッダを送信する
     *
     * @param string $header
     * @param bool $replace
     * @param int $code
     */
    public function sendHeader( $header, $replace = true,  $code = false )
    {
        if( $code !== false )
        {
            header( $header, $replace, $code );
        }
        else
        {
            header( $header, $replace );
        }

    }

    /**
     * システムを停止する
     *
     * @param string
     */
    public function halt( $message = null)
    {
        if( $this->fake_exit === false )
        {
            exit($message);
        }

        echo $message;
    }

    /**
     * システムを停止させない
     *
     * @param bool
     */
    public function fakeExit( $flg = true)
    {
        $this->fake_exit = $flg;

        return $this;
    }


    /**
     * エラーレポーティング
     *
     * @param bool
     * @return object $this
     */
    public function errorReporting( $flg )
    {
        error_reporting( $flg );
        return $this;
    }

    /**
     * エラーを画面に表示する
     *
     * @param bool
     * @return object $this
     */
    public function displayErrors( $flg = true )
    {
        return $this->iniSet('display_errors', ($flg ? 1: 0));
    }

    /**
     * INIをセットする
     *
     * @param string $name
     * @param string $value
     * @return object $this
     */
    public function iniSet( $name, $value )
    {
        ini_set($name, $value);
        return $this;
    }

    /**
     * 言語設定
     *
     * @param string $lang 
     * @return object $this
     */
    public function setLang( $lang = "ja")
    {
        mb_language( $lang );
        mb_internal_encoding( 'utf8' );
        return $this;
    }

    /**
     * エラーハンドラをセットする
     *
     * @param mixd $func
     * @return object $this
     */
    public function setErrorHandler( $func )
    {
        set_error_handler( $func );
        return $this;
    }

    /**
     * 例外ハンドラ
     *
     * @param mixd $func
     * @return object $this
     */
    public function setExceptionHandler( $func )
    {
        set_exception_handler( $func );
        return $this;
    }

    /**
     * スクリプト終了時に呼び出されるハンドラを登録する
     *
     * @param mixd $func
     */
    public function setShutdownFunction( $func )
    {
        register_shutdown_function( $func );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
