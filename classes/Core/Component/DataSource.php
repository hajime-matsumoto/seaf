<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;

class DataSource extends Seaf\DataSource\DataSource
{
    use ComponentTrait;


    protected function _componentHelper ($name)
    {
        return $this->getDataSourceHandler($name);
    }


}
