<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Data\Helper;

use Seaf\Data;

class NullHelper extends Data\Helper
{
    public function __construct ($name, $parent = null)
    {
        $this->init($name, $parent);
    }

    public function __get($name)
    {
        return new NullHelper($name, $parent);
    }
}
