<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ロギングモジュール
 */
namespace Seaf\Logging\Filter;

use Seaf\Logging;
use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;

/**
 * モジュールファサード
 */
class LevelFilter
{
    use ConfigureTrait;


    private $level;

    /**
     * コンストラクタ
     */
    public function __construct ($level)
    {
        $this->level = Logging\Level::parse($level);
    }

    public function filter($log)
    {
        return $this->level & $log->get('level');
    }
}
