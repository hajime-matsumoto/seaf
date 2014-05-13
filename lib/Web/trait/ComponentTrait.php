<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Module;

/**
 * コンポーネント
 */
trait ComponentTrait
    {
        use Module\ModuleFacadeTrait;

        public function setParentWebComponent(ComponentIF $component)
        {
            $this->setParentModule($component);
        }
    }
