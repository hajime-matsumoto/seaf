<?php

namespace Seaf\Core\Component;

use Seaf\Core\Environment;
use Seaf\Log\Level;


/**
 * ログコンポーネント
 */
class LogComponent extends Environment
{
    private $name;

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
     * initComponent
     *
     * @param Environment
     * @return void
     */
    public function initComponent (Environment $env)
    {
        $this->name = $env->get('name');

        $this->map('post', '_post');
    }

    /**
     * @param array
     * @param int
     */
    public function _post ($context,$level)
    {
        $context['name'] = $this->name;
        Seaf::log()->post($context, $level);
    }

    public  function __call ($name, $params) 
    {
        if (array_key_exists($name, self::$methods)) {
            $this->post($params,self::$methods[$name]);
        } else {
            return parent::__call($name,$params);
        }
    }
}
