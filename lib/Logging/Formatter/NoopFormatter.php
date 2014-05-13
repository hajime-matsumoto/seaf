<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ロギングモジュール
 */
namespace Seaf\Logging\Formatter;

use Seaf\Logging;
use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;

/**
 * モジュールファサード
 */
class NoopFormatter
{
    use ConfigureTrait;


    /**
     * コンストラクタ
     */
    public function __construct (array $setting)
    {
    }

    public function format($log)
    {
        return (string) $log;
    }
}
