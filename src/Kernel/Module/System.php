<?php
namespace Seaf\Kernel\Module;

use Seaf;
use Seaf\Core\Pattern\ExtendableMethods;

class System extends Module
{
    use ExtendableMethods {
        ExtendableMethods::call as __call;
    }

    public function initModule ( )
    {
        $this->map(array(
            'halt' =>  '_halt',
            'header' =>  '_header'
        ));
    }

    public function _halt ($body = 0)
    {
        exit($body);
    }

    public function _header($string, $replace = null, $code = null)
    {
        if ($replace === null && $code === null) {
            header($string);
        }elseif($code === null){
            header($string, $replace);
        }else{
            header($string, $replace, $code);
        }
        Seaf::logger()->debug(array(
            "Header Setn: %s %s %s", $string, $replace, $code
        ));
    }
}
