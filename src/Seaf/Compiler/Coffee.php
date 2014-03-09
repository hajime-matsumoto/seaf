<?php

namespace Seaf\Compiler;

class Coffee extends Compiler
{
    public function buildCommand ( )
    {
        return 'coffee -c -p -s';
    }
}
