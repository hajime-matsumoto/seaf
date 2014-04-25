<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

use Seaf\Container;
use Seaf\Wrapper;

/**
 * ログフィルター
 */
class Filter
{

    /**
     * Filterを作成
     */
    public static function factory ($cfg)
    {
        $cfg = new Container\ArrayContainer($cfg);

        $type = $cfg('type', 'level');

        return Wrapper\ReflectionClass::create(
            __NAMESPACE__.'\\Filter\\'.ucfirst($type).'Filter'
        )->newInstanceArgs([$cfg]);
    }

}
