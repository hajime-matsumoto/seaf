<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * システムモジュール
 */
namespace Seaf\System;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;
use Seaf\Base\Module;

/**
 * モジュールファサード:メディエータ
 */
class SystemFacade implements Module\ModuleMediatorIF
{
    use Module\ModuleMediatorTrait;

    protected static $object_name = 'system';

    public function __construct(Module\ModuleIF $module  = null)
    {
        if ($module) {
            $this->setParentModule($module);
        }

        $this->registerModule('superGlobals', 'Seaf\System\SuperGlobalsFacade');
    }
}
