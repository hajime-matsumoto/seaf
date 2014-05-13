<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Module;

class ModuleFacade implements ModuleFacadeIF
{
    use ModuleFacadeTrait;

    protected static $object_name = 'ModuleFacade';

    public function __construct (ModuleIF $module)
    {
        if ($module instanceof ModuleIF) {
            $this->setParentModule($module);
        }
    }
}
