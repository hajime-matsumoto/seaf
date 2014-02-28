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

use Seaf\DI\DIContainer;
use PhpConsole\Dumper as PHPConsoleDumper;
use PhpConsole\Connector as PHPConsoleConnector;

/**
 * Debuggerコンポーネント
 */
class Debugger
{
    private $di;
    private $dumper;

    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        $this->di = $di;
    }

    public function __construct( )
    {
        $this->dumper = new PHPConsoleDumper();
    }

    public function dump( $var )
    {
        var_dump( $this->dumper->dump( $var ) );
    }

    public function debug( $var )
    {
        PHPConsoleConnector::getInstance()->getDebugDispatcher()->dispatchDebug(
            $var, null, 1
        );
    }

    public function register( )
    {
        $this->di->get('helperHandler')->bind($this,array(
            'd'=>'debug',
            'dump'=>'dump'
        ));
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
