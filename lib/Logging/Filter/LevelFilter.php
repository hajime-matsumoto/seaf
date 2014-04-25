<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging\Filter;

use Seaf\Logging;
use Seaf\Container;
use Seaf\Wrapper;

/**
 * ログフィルター
 */
class LevelFilter extends Logging\Filter
{
    private $level;

    public function __construct ($cfg)
    {
        $this->level = Logging\Code\LogLevel::parse($cfg['value']);
    }

    public function filter(Logging\Log $Log)
    {
        return ($this->level & $Log->level) > 0;
    }

}
