<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Base\Singleton;
use Seaf\Base\Container;

class Root extends Observable implements Singleton\SingletonIF, ObserverIF
{
    use Singleton\SingletonTrait;

    private $events;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct ( )
    {
        $this->events = new Container\ArrayContainer();
    }

    public function notify (Event $e)
    {
        foreach ($this->events->getArray($e->type) as $callback) {
            $callback($e);
        }
        foreach ($this->getObservers() as $observer)
        {
            $observer->notify($e);
        }

    }

    public function bind ($type, $callback)
    {
        $this->events->add($type, $callback);
    }
}

