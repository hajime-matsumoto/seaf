<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Console;

use Seaf\FrameWork;

/**
 * Application
 */
class Application extends FrameWork\Application
{
    private $descs = array();

    public function exec($cmd, $buf = false)
    {
        $this->system()->out('Exec Command: '.$cmd."\n");
        $return = $this->system()->execute($cmd, $buf);
        $this->system()->out("\n");
        return $return;
    }

    public function out ($body) 
    {
        $this->system()->out($body."\n");
    }

    public function in ($body, $default) 
    {
        $body .= '['.$default.']';
        $this->system()->out($body);
        $return =  $this->system()->in();
        if (empty($return)) {
            return $default;
        }
    }

    public function initApplication()
    {
        $class = new \ReflectionClass($this);
        foreach($class->getMethods() as $method){
            if ($method->getDeclaringClass()->getName() == get_class($this)) {
                $comment = $method->getDocComment();
                $line = preg_split("/\n/", $comment);
                $route = false;
                $desc = false;
                for ($i=1;$i<(count($line)-1);$i++) {
                    if (preg_match('#[^@]+@Seaf([^\s]+)\s+(.+)#',$line[$i],$m)) {
                        if ($m[1] == 'Route') {
                            $route = $m[2];
                        }
                        if ($m[1] == 'Desc') {
                            $desc = $m[2];
                        }
                    }
                }

                if ($route) {
                    $this->route($route, $method->getClosure($this));
                }
                if ($desc) {
                    $this->descs[$route] = $desc;
                }
            }
        }
    }

    public function notfound( )
    {
        $this->out('Usage');
        $this->out('--------------------------');
        $this->out(basename($GLOBALS['argv'][0]));
        foreach ($this->descs as $k => $v) {
            $this->out("\t".$k.' : '.$v);
        }
    }
}
