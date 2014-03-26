<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\Compiler\Command;

use Seaf;

class Sass extends Base
{
    public function __construct( )
    {
        $this->opts = array(
            '--compass',
            '--cache-location'=>Seaf::Config('dirs.cache','/tmp/sass'),
            '-s',
            '-E'=>'utf-8'
        );
    }

    public function buildCommand ( )
    {
        return 'sass '.$this->buildOpts();
    }
}
