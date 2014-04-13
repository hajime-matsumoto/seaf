<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Event;

use Seaf\Container\ArrayContainer;

class Event
{
    public $target;
    public $params = [];

    public function __construct ($target, $params = [])
    {
        $this->target = $target;
        $this->params = $params;
    }

    public function __get ($name)
    {
        return $this->params[$name];
    }

}
