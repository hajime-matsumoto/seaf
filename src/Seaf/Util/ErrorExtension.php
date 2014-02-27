<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */


namespace Seaf\Util;

use Seaf\Core\Extension;
use Seaf\Core\Base;

/**
 * エラーハンドリングエクステンション
 *
 * @SeafClassType   Extension
 * @SeafInitialize  initializeExtension
 */
class ErrorExtension extends Extension
{
    private $errHandler;
    private $excHandler;

    public function initializeExtension( $prefix, $base )
    {
        $this->errHandler = array($this,'_errorHandler');
        $this->excHandler = array($this,'_exceptionHandler');

        set_error_handler( array($this,'errorHandler'));
        set_exception_handler( array($this,'exceptionHandler'));
    }

    /**
     * @SeafBindPrefix false
     * @SeafBind setErrorHandler
     */
    public function setErrorHandler($function)
    {
        $this->errHandler = $function;
    }

    /**
     * @SeafBindPrefix false
     * @SeafBind setExceptionHandler
     */
    public function setExceptionHandler($function)
    {
        $this->excHandler = $function;
    }

    public function errorHandler( )
    {
        return DispatchHelper::invokeArgs( $this->errHandler, func_get_args() );
    }

    public function exceptionHandler( )
    {
        return DispatchHelper::invokeArgs( $this->excHandler, func_get_args() );
    }

    public function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return;
        }

        printf('[%d] %s %s %s',
            $errno,
            $errstr,
            $errfile,
            $errline
        );
    }

    public function _exceptionHandler( $e )
    {
        echo '<pre>';
        echo $e;
        echo '</pre>';
    }
}
/* vim: set expandtab ts=4 sw=4 sts=4: */
