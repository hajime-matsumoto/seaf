<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger\Writer;

use Seaf\Container\ArrayContainer;
use Seaf\Logger;

class CallbackWriter extends Logger\Writer
{
    public function __construct($cb)
    {
        $this->cb = $cb;
    }

    public function post( )
    {
        call_user_func_array($this->cb, func_get_args());
    }
}
