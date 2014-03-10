<?php

namespace Seaf\Core\Component;

use Seaf\Core\Environment;
use Seaf\Core\Log\Level;


/**
 * ログコンポーネント
 */
class LogComponent
{
    // 呼び出しを許可するメソッド
    private static $methods = array(
        'emerg'    => Level::EMERGENCY,
        'alert'    => Level::ALERT,
        'critical' => Level::CRITICAL,
        'error'    => Level::ERROR,
        'warn'     => Level::WARNING,
        'info'     => Level::INFO,
        'debug'    => Level::DEBUG
    );

    /**
     * __construct
     *
     * @param Environment $env
     */
    public function __construct ( )
    {
    }

    /**
     * initComponent
     *
     * @param Environment
     * @return void
     */
    public function initComponent (Environment $env)
    {
    }

    public function post ($context,$level)
    {
        var_dump($context, $level);
    }

    public  function __call ($name, $params) 
    {
        if (array_key_exists($name, self::$methods)) {
            $this->post($params,self::$methods[$name]);
        }
    }
}
