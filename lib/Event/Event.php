<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Event;

use Seaf\Container;

class Event extends Container\ArrayContainer
{
    public function __construct ($name, $params)
    {
        $this->name = $name;
        parent::__construct($params);
    }
}
