<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\Base\Component;
use Seaf\Base\Module;


/**
 * WEBコンポーネントのインターフェイス
 */
interface ComponentIF extends Component\ComponentIF, Module\ModuleIF
{
    /**
     * 親を設定
     */
    public function setParentWebComponent(ComponentIF $component);
}
