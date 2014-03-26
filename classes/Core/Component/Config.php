<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Data\Config\Base;

/**
 * コンフィグ
 */
class Config extends Base
{
    /**
     * 環境名を取得する
     */
    protected function productionMode ( )
    {
        return Seaf::$production_mode;
    }

    /**
     * ヘルパ
     */
    public function helper ($config = null, $default = null)
    {
        if ($config == null) {
            return $this;
        }

        return $this->get($config, $default);
    }
}
