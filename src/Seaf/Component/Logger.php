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
use Seaf\Collection\ArrayCollection;
use Seaf\Logger\Logger as SeafLogger;

/**
 * Loggerコンポーネント
 */
class Logger extends SeafLogger
{
    private $di;

    /**
     * @param object $di
     */
    public function acceptDIContainer( DIContainer $di )
    {
        $this->di = $di;

        $di->depends('system');

        $di->get('system')->setErrorHandler(array($this,'phpErrorHandler'));
        $di->get('system')->setShutdownFunction(array($this,'shutdownFunction'));
    }

    public function register( )
    {
        $this->di->get('helperHandler')->bind($this,array(
            'fatal' => 'fatal',
            'err'   => 'err',
            'warn'  => 'warn',
            'info'  => 'info',
            'debug' => 'debug'
        ));
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
