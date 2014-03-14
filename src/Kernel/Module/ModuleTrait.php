<?php
namespace Seaf\Kernel\Module;

use Seaf\Kernel\Kernel;

trait ModuleTrait
{
    public function __construct (Kernel $kernel)
    {
        $this->initModule($kernel);
    }

    abstract public function initModule (Kernel $kernel);
}
