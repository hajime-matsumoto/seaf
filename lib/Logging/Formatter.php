<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

use Seaf\Container;
use Seaf\Wrapper;

/**
 * ログフォーマッタ
 */
class Formatter
{
    public function __construct ($cfg)
    {
        var_Dump($cfg);
    }

    /**
     * Writerを作成
     */
    public static function factory ($cfg)
    {
        $cfg = new Container\ArrayContainer($cfg);

        $type = $cfg('type', 'text');

        return Wrapper\ReflectionClass::create(
            __NAMESPACE__.'\\Formatter\\'.ucfirst($type).'Formatter'
        )->newInstanceArgs([$cfg]);
    }

}
