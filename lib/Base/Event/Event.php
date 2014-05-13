<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Event;

use Seaf\Util\Util;

class Event implements EventIF
{
    private $isStoped = false;
    private $info;

    public function __construct ($type, $params, $source)
    {
        $this->info = Util::Dictionary([
            'type' => $type,
            'source' => $source,
            'params' => $params
        ]);
    }

    public function stop() {
        $this->isStoped = true;
    }

    public function isStoped() {
        return $this->isStoped;
    }

    public function getType() {
        return $this->info->type;
    }

    public function getParams() {
        return $this->info->get('params');
    }


    public function getSource() {
        return $this->info->source;
    }

    public function addCallers($caller) {
        return $this->info->prepend('caller', $caller);
    }

    public function getCallers( )
    {
        return $this->info->get('caller', []);
    }

    public function __get($name)
    {
        return $this->info->dict('params')->get($name);
    }
}
