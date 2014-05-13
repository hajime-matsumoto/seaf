<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * モジュール
 */
namespace Seaf\Base\Module;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\BackEnd;
use Seaf\Logging;

/**
 * リクエスト
 */
abstract class ModuleMediator implements ModuleMediatorIF
{
    use ModuleMediatorTrait;

    public function __construct (ModuleIF $module = null, $configs = [])
    {
        if ($module) {
            $this->setParentModule($module);
        }
    }
}
