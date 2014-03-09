<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Environment\Component;

use Seaf\Environment\Environment;

/**
 * イベント管理
 */
class Event
{
    private $events;

    /**
     * @var Environment
     */
    private $env;

    public function __construct (Environment $env) 
    {
        $this->env = $env;

        $this->init();
    }

    public function init ( )
    {
        $this->events = array();
    }

    /**
     * イベントを登録する
     */
    public function on ($event_name, $action)
    {
        if (is_string($action) && !is_callable($action)) {
            $action = array($this->env,$action);
        }

        $this->events[$event_name][] = $action;

        return $action;
    }

    /**
     * イベントを解除する
     */
    public function off ($event_name, $action)
    {
        if (!isset($this->events[$event_name])) return $this;

        foreach ($this->events[$event_name] as $key => $registered_action) {
            if ($action == $registered_action) {
                unset($this->events[$event_name][$key]);
            }
        }
    }

    /**
     * イベントを発生させる
     */
    public function trigger ($event_name)
    {
        if (!isset($this->events[$event_name])) return $this;

        $args = func_get_args();
        array_shift($args);
        array_push($args, $this->env);
        foreach ($this->events[$event_name] as $action) {
            $result = call_user_func_array($action, $args);
        }
    }
}
