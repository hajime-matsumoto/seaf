<?php

namespace Seaf\Misc\Compiler;

class SassCompiler extends Compiler
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

    public function setOpt($name, $value, $replace = false)
    {
        if ($replace == false) {
            $this->opts[$name][] = $value;
        }else{
            $this->opts[$name] = $value;
        }
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
