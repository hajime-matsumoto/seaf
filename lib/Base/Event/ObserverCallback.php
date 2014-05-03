<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Base\Container;

class ObserverCallback implements ObserverIF
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function notify (Event $e)
    {
        $cb = $this->callback;
        return $cb($e);
    }
}

