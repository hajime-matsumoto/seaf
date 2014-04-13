<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

use Seaf\Event;

trait EventTrait
{
    private $eventObservable = false;

    private function event( ) 
    {
        if ($this->eventObservable) {
            return $this->eventObservable;
        }else{
            return $this->eventObservable = new Event\Observable($this);
        }
    }

    public function trigger($name, $params = [])
    {
        $this->event()->trigger($name, $params);
    }

    public function on ($name, $action = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->on($k, $v);
            return $this;
        }
        if (is_string($action)) $action = [$this, $action];

        $this->event()->on($name, $action);
        return $this;
    }

    public function off ($name, $action)
    {
        $this->event()->on($name, $action);
    }
}
