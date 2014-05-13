<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ユーティリティモジュール
 */
namespace Seaf\Util;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Module;

/**
 * ファサード:メディエータ
 */
class UtilFacade implements Module\ModuleMediatorIF
{
    use Module\ModuleMediatorTrait;

    protected static $object_name = 'Util';

    public function __construct(Module\ModuleIF $module  = null)
    {
        if ($module) {
            $this->setParentModule($module);
        }

        $this->registerModule([
            'phpFunction' => 'Seaf\Util\PHPFunctionFacade',
            'Annotation' => 'Seaf\Util\Annotation\AnnotationFacade',
        ]);
    }
}
