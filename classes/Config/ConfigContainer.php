<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Config;

use Seaf\Container\ArrayContainer;

class ConfigContainer extends ArrayContainer
{
    public function __construct ($values)
    {
        $this->set($values);
    }
}
