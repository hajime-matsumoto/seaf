<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Net\Request\Initializer;

use Seaf\Net\Request;
use Seaf\Kernel\Kernel;

class Cli extends Request\Initializer
{
    public function init ()
    {
        // オプションを解析する
        self::parseOpts( Kernel::Globals()->get('argv'), $result, $args);

        $uri = array_shift($args);
        $this->base->uri = $args;

        $params = array();
        foreach ($result as $k=>$v) 
        {
            if (isset($params[$k])) {
                if (!is_array($params[$k])) {
                    $params[$k] = array($params[$k]);
                }
                $params[$k][] = $v;
            }else{
                $params[$k] = $v;
            }
        }

        foreach ($args as $k=>$v)
        {
            $this->base->params[$k] = $v;
        }


        $this->base->params = $params;
    }


    /**
     * オプションを解析する関数
     */
    private static function parseOpts ($opts, &$result, &$args)
    {
        $fileName = array_shift($opts);
        $inSwitch = true;
        $args     = array();
        $result   = array();
        for ($i=0; $i<count($opts); $i++) {
            $p = $opts[$i];

            if ($p == '--') {
                $inSwitch = false;
                continue;
            }

            if ($inSwitch == true && $p{0} == '-') {
                $pname = substr($p, 1, 1);
                if (strlen($p) > 2) {
                    $value = substr($p,2);
                } else {
                    $value = true;
                }

                if ($pname{0} == '-') {
                    $pname = substr($p, 2);
                    if ($opts[$i+1]{0} == '-') {
                        $value = true;
                    } else {
                        $value = $opts[$i+1];
                        $i++;
                    }
                }
                $result[] = array($pname, $value);
            } else {
                $args[] = $p;
            }
        }
    }
}
