<?php
namespace Seaf\Environment\Component;

use Seaf\Logger\Logger as Base;
use Seaf\Logger\Level;
use Seaf\Kernel\Kernel;
use Seaf\Environment\Environment;

/**
 * Component用のロガー
 * カーネルのロガーと接続する
 */
class Logger extends Base implements ComponentIF
{
    use ComponentTrait;

    private $tag;
    private $env;

    public function initComponent(Environment $env)
    {
        $this->tag = get_class($env);
        $this->env = $env;
    }

    /**
     * post
     *
     * @param $context, $level
     * @return void
     */
    public function post ($context, $level, $tag = false, $trace = null)
    {
        if ($tag == false) $tag = $this->tag;

        // 特定のレベルだったらデバッグトレースを渡す
        if ($trace == null && ((Level::DEBUG | Level::ERROR | Level::EMERGENCY) & $level)) {
            $trace   = array_slice(debug_backtrace(),1);
        }


        Kernel::logger()->post($context, $level, $tag, $trace);
    }

    public function importHelper ( )
    {
        foreach (Level::$map as $k=>$v) {
            $v = strtolower($v);
            $this->env->bind($this, array($v => $v));
        }
    }

}
