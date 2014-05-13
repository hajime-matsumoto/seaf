<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ロギングモジュール
 */
namespace Seaf\Logging;

use Seaf\Util\Util;
use Seaf\Base\Module;

/**
 * モジュールファサード
 */
class LoggingFacade implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;

    protected static $object_name = 'Logging';

    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $module = null, $configs = [])
    {
        if ($module) {
            $this->setParentModule($module);
        }

        $this->handlers = Utill::Dictionary();
    }

    public function handler($name = 'default')
    {
        if (!$this->handlers->has($name)) {
            $this->handlers[$name] = new LogHandler($this);
        }
    }

    public function enable( )
    {
        $this->isEnabled = true;
    }

    public function addWriter($writer)
    {

    }
}
