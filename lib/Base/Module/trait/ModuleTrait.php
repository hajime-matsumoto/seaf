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
use Seaf\Base;
use Seaf\Logging;
use Seaf\Base\Component;

/**
 * モジュールファサード
 */
trait ModuleTrait
    {
        use Component\ComponentTrait;


        public function root ( )
        {
            return $this->rootParent;
        }

        public function mediator ( )
        {
            return $this->findParent(function($p){ return $p->isMediator(); });
        }

        public function isMediator( )
        {
            if ($this instanceof ModuleMediatorIF) return true;
            return false;
        }

        public function setParentModule(ModuleIF $module)
        {
            $this->setParentComponent($module);
        }
    }
