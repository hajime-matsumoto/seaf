<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;

class Cache extends Seaf\Cache\Cache
{
    use ComponentTrait;


    protected function _componentHelper ($name)
    {
        return $this->getHandler($name);
    }


}
