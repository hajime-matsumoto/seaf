<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Base\Container;

class ObserverCallback implements ObserverIF
{
    private $callback;
    use ObserverTrait;

    public function __construct($callback)
    {
        $this->initObserver();
        $this->setNotifyHandler($callback);
    }
}

