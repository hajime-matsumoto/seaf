<?php

namespace Seaf\Misc\Compiler;

class Coffee extends Compiler
{
    public function buildCommand ( )
    {
        return 'coffee -c -p -s';
    }
}
