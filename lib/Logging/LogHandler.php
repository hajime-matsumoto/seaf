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
class LogHandler implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;

    protected static $object_name = 'LogHandler';

    private $writers;

    /**
     * コンストラクタ
     */
    public function __construct ($module = null, $configs = [])
    {
        $this->writers = Util::Dictionary();

        $module->on('log', function($e) {
            $this->logPost($e->log);
            $e->stop();
        });
    }

    public function addWriter($setting)
    {
        $type = Util::Dictionary($setting)->get('type', 'echo');
        $class = Util::ClassName(__NAMESPACE__, 'Writer', $type.'Writer');
        $writer = $class->newInstance($this, $setting);
        $name = spl_object_hash($writer);
        $this->writers->set($name, $writer);
    }

    public function logPost($log)
    {
        foreach ($this->writers as $writer) {
            $writer->logPost($log);
        }
    }

}
