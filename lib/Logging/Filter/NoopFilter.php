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
class NoopFilter
{
    use ConfigureTrait;


    /**
     * コンストラクタ
     */
    public function __construct (array $setting)
    {
    }

    public function filter( )
    {
        return true;
    }
}
