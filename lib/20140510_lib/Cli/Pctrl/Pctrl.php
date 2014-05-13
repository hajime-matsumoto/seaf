<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Cli\Pctrl;

use Seaf\Base\Singleton;
use Seaf\Base\Event;
use Seaf\Logging;

/**
 * ProcessController
 */
class Pctrl implements Singleton\SingletonIF,Event\ObservableIF
{
    use Singleton\SingletonTrait;
    use Logging\LoggableTrait;
    use Event\ObservableTrait;

    protected $_max_process = 3;
    protected $_timeout = 5;
    protected $_args;
    protected $_stack;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct( )
    {
        $this->_args = array();
        $this->_stack = array();

        pcntl_signal(SIGALRM, function($signal) {
            $this->debug("PCTRL", "Get Signal {".$signal."}");
            switch ($signal) {
            case SIGALRM:
                echo ">>>> TIMEOUT!! <<<<\n";
                exit();
                break;
            default:
                break;
            }
        });
    }

    public function setMaxProcess($max_process)
    {
        $this->_max_process = $max_process;
    }

    public function setTimeout($sec)
    {
        $this->_timeout = $sec;
    }

    public function addArgs($args)
    {
        $this->_args[] = $args;
    }

    public function clearArgs( )
    {
        $this->_args = [];
    }

    public function runAll(callable $callback)
    {
        if (!is_callable($callback)) {
            throw new \Exception('Not callable ['.$callback.']');
        }

        foreach ($this->_args as $args) {
            $pid = pcntl_fork();
            if (-1 === $pid) {
                throw new \Exception('False fork process ['.pcntl_get_last_error().']');
            }

            if ($pid) {
                $this->debug("PCTRL", "[".__METHOD__."]: Parent process PID [".$pid."].");
                $this->_stack[$pid] = true;
                if (count($this->_stack) >= $this->_max_process) {
                    $this->debug("PCTRL", "[".__METHOD__."]: Stacked process is max ...waiting...[".count($this->_stack)."].");
                    unset($this->_stack[pcntl_waitpid(-1, $status, WUNTRACED)]);
                }
            } else {
                $this->debug("PCTRL", "[" . __METHOD__ . "]: Child process running.");
                pcntl_alarm($this->_timeout);
                call_user_func_array($callback, $args);
                exit();
            }
        }

        // すべての子プロセスの終了を待つ
        while (count($this->_stack) > 0) {
            $this->debug("PCTRL", "[" . __METHOD__ . "]: Waiting process all end...[".count($this->_stack)."].");
            unset($this->_stack[pcntl_waitpid(-1, $status, WUNTRACED)]);
        }
    }
}
