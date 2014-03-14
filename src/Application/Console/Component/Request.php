<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Application\Console\Component;

use Seaf\Application\Component\Request as RequestBase;
use Seaf\Kernel\Kernel;

/**
 * アプリケーションリクエスト
 * ===========================
 *
 * 要件
 * --------------------------
 * * getURI
 * * getMethod
 */
class Request extends RequestBase
{
    private $opts;

    public function initRequest ( ) 
    {
        $this->opts = array();


        $this->parseOpts( Kernel::Globals()->get('argv'), $result, $args);

        //var_dump(Kernel::Globals()->get('argv'));
        //var_dump($result, $args);

        $uri = array_shift($args);
        $this->setUri($uri);

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
        $this->setParam($params);
    }


    public function parseOpts ($opts, &$result, &$args)
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
