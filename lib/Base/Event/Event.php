<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Base\Container;

class Event
{
    use Container\ArrayContainerTrait;

    public $type;
    public $target;

    public function __construct ($type, $args, $target)
    {
        $this->type   = $type;
        $this->target = $target;
        $this->data = $args;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

}
