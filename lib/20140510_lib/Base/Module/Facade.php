<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Module;

use Seaf\Base\Command;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * 
 */
class Facade implements FacadeIF,Event\ObservableIF
{
    use Logging\LoggableTrait;
    use Event\ObservableTrait;

    /**
     * @See FacadeIF
     */
    public function execute (Command\RequestIF $request, $from = null)
    {
        $method = $request('method');
        $args   = $request('args');

        // 実行を記録させる
        $this->fireEvent(
            'execute', [
                'method' => $method,
                'args' => $args
            ]
        );

        if (!is_callable([$this, $method])) {
            // メソッドが見つからないエラー
            return $request->result()->error("METHOD_NOTFOUND",[
                'method' => $method,
                'class' => get_class($this)
            ]);
        }

        $request->result()->addReturnValue(
            call_user_func_array([$this, $method], $args)
        );

        return $request->result();
    }

    public function initWithMediator($mediator)
    {
    }

}
