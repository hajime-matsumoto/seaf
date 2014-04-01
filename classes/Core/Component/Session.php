<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Pattern;
use Seaf\Session\Handler as Base;

/**
 * セッション
 */
class Session extends Base
{
    use Pattern\Configure;

    public $name = 'Seaf';

    /**
     * 作成するメソッド
     *
     * @param array
     */
    public static function componentFactory ( )
    {
        $c = Seaf::Config('session.storage');
        $session = self::factory($c);
        return $session;
    }
}
