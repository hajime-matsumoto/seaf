<?php
namespace Seaf\Environment\Component;

use Seaf\Logger\Logger as Base;
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

    public function initComponent(Environment $env)
    {
        $this->tag = get_class($env);
    }

    /**
     * post
     *
     * @param $context, $level
     * @return void
     */
    public function post ($context, $level, $tag = false)
    {
        if ($tag == false) $tag = $this->tag;
        Kernel::logger()->post($context, $level, $tag);
    }
}
