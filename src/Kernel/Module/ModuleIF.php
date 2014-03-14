<?php
namespace Seaf\Kernel\Module;

use Seaf\Kernel\Kernel;

interface ModuleIF
{
    public function initModule (Kernel $kernel);
}
