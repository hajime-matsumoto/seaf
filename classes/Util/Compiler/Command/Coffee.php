<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\Compiler\Command;

class Coffee extends Base
{
    public function buildCommand ( )
    {
        return 'coffee -c -p -s';
    }
}
