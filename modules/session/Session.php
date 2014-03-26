<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Session;

use Seaf;
use Seaf\Data\Container\ArrayContainer;

class Session
{
    /**
     * Sessionを作成する
     */
    public static function factory ($config)
    {
        $c = new ArrayContainer($config);
        $type = $c('type', 'file');

        $class = __NAMESPACE__.'\\Handler\\'.ucfirst($type).'Handler';

        $session = new $class();
        return $session;
    }
}
