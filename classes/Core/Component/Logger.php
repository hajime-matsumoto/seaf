<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Logger\Writer;

class Logger extends Seaf\Logger\Logger
{
    use ComponentTrait;

    public function __construct ($cfg)
    {
    }


    public function attach($level, $callback)
    {
        return $this->attachWriter(new Writer\CallbackWriter($callback));
    }

}
