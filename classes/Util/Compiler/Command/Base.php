<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\Compiler\Command;

use Seaf;

abstract class Base
{
    protected $opts = array();

    public function __construct( )
    {

    }

    public function setOpt($name, $value = false, $replace = false)
    {
        if ($value == false) $value = $name;

        if ($replace == false) {
            $this->opts[$name][] = $value;
        }else{
            $this->opts[$name] = $value;
        }
        return $this;
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

    public function compile ($file)
    {
        $files = func_get_args();
        $desc = array(
            0 => array('pipe','r'),
            1 => array('pipe','w'),
            2 => array('pipe','w')
        );
        $cmd = $this->buildCommand();
        Seaf::logger('compiler')->debug("Execute:" . $cmd . " files: ".implode(" ", $files));

        $proc = proc_open($cmd, $desc, $pipes);

        foreach($files as $file) {
            fwrite($pipes[0], Seaf::fileSystem($file)->getContents());
        }
        fclose($pipes[0]);

        echo stream_get_contents($pipes[2]);
        echo stream_get_contents($pipes[1]);

        $return = proc_close($proc);
        return $return;
    }

    abstract protected function buildCommand();

    public function __invoke ($file)
    {
        return $this->compile($file);
    }
}
