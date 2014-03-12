<?php

namespace Seaf\Misc\Compiler;

class CoffeeCompiler extends Compiler
{
    public function buildCommand ( )
    {
        return 'coffee -c -p -s';
    }
}
