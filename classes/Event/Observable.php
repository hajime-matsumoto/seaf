<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Event;

use Seaf\Container\ArrayContainer;

class Observable
{
    private $events;

    public function __construct ($target)
    {
        $this->events = [];
        $this->target = $target;
    }

    public function trigger($name, $params = [])
    {
        $event = new Event ($this->target, $params);

        if (
            isset($this->events[$name]) &&
            is_array($this->events[$name])
        ) foreach ($this->events[$name] as $eventCallback) {
            $con = call_user_func($eventCallback, $event);

            if ($con === false) break;
        }
    }

    public function on($name, $action, $prepend = false)
    {
        if (!isset($this->events[$name])) {
            $this->events[$name] =  [];
        }
        if ($prepend) {
            array_unshift($this->events[$name], $action);
        }else{
            array_push($this->events[$name], $action);
        }
    }
}
