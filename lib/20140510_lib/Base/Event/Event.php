<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Base\Container;

class Event extends Container\ArrayContainer implements EventIF
{
    private $isStoped = false;

    public function __construct ($type, $args, $target)
    {
        parent::__construct([
            'type'   => $type,
            'target' => $target,
            'params' => $args
        ]);
    }

    public function getTargetClass( )
    {
        return get_class($this->get('target'));
    }

    public function getParam($name)
    {
        return $this->dict('params')->get($name);
    }

    public function isType($type)
    {
        return strtolower($type) == $this('type');
    }

    public function inType($types)
    {
        return in_array($this('type'), $types);
    }

    public function stop() {
        $this->isStoped = true;
    }

    public function isStop() {
        return $this->isStoped;
    }
}
