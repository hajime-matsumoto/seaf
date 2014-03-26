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
    public $name = 'Seaf';

    use Pattern\Factory;

    public function helper($name = null)
    {
        if ($name == null) return $this;
        return $this->getHandler($name);
    }
}
