<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Pattern;
use Seaf\Logger\Base;

/**
 * ロガー
 */
class Logger extends Base
{
    use Pattern\Configure;

    public $name = 'Seaf';

    /**
     * 作成するメソッド
     *
     * @param array
     */
    public static function componentFactory ($config = [])
    {
        $c = Seaf::Config('logger') + $config;
        $logger = new static();
        $logger->configure($c);
        return $logger;
    }


    public function helper($name = null)
    {
        if ($name == null) return $this;
        return $this->getHandler($name);
    }
}
