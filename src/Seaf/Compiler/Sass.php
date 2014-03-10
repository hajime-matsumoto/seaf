<?php

namespace Seaf\Compiler;

class Sass extends Compiler
{
    public function __construct( )
    {
        $this->opts = array(
            '--compass',
            '--cache-location'=>'/tmp/sass',
            '-s',
            '-E'=>'utf-8'
        );
    }

    public function setOpt($name, $value)
    {
        $this->opts[$name][] = $value;
    }

    public function buildOpts( )
    {
        $opts = array();
        foreach($this->opts as $k => $v) {
            if ($k{0} != "-") {
                $opts[] = $v;
            }else{
                if (is_array($v)) {
                    foreach ($v as $vv) {
                        $opts[] = "$k \"$vv\"";
                    }
                }else{
                    $opts[] = "$k \"$v\"";
                }
            }
        }
        return implode(" ", $opts);
    }

    public function buildCommand ( )
    {
        return 'sass '.$this->buildOpts();
    }
}