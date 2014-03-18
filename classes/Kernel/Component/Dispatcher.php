<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Kernel\Component;


/**
 * ディスパッチャ
 */
class Dispatcher
{
    public function helper ($func = null, $params = null)
    {
        if ($func == null) return $this;

        if (is_callable($func)) {
            $command = new DispatcherCommand($func, $params);
            return $command;
        }
    }

}

class DispatcherCommand
{
    protected $func;
    protected $params;

    public function __construct ($func, $params)
    {
        $this->func = $func;
        $this->params = $params;
    }

    public function dispatch ( )
    {
        return call_user_func_array($this->func, $this->params);
    }
}


