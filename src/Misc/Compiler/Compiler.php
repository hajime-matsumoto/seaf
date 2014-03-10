<?php

namespace Seaf\Misc\Compiler;

use Seaf\Core\Kernel;

abstract class Compiler
{
    abstract public function buildCommand();

    public function compile ($file)
    {
        $files = func_get_args();
        $desc = array(
            0 => array('pipe','r'),
            1 => array('pipe','w'),
            2 => array('pipe','w')
        );
        $cmd = $this->buildCommand();
        //Seaf::debug("Execute:" . $cmd . " files: ".implode(" ", $files));

        $proc = proc_open($cmd, $desc, $pipes);

        foreach($files as $file) {
            fwrite($pipes[0], Kernel::fs()->getContents($file));
        }
        fclose($pipes[0]);

        echo stream_get_contents($pipes[2]);
        echo stream_get_contents($pipes[1]);

        $return = proc_close($proc);
        return $return;
    }

}
