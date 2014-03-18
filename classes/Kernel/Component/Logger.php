<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Kernel\Component;

use Seaf\Logger\Base;

/**
 * Kernelに仕込むロガー
 */
class Logger extends Base
{
    public $name = "Kernel";

    public function __construct ( )
    {
        parent::__construct();

        $this->register();
    }
}
