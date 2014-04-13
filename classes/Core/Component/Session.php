<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf\Base;

class Session extends \Seaf\Session\Session
{
    use ComponentTrait;


    public function _componentHelper ($name)
    {
        return $this->helper($name);
    }
}
