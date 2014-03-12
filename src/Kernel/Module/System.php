<?php
namespace Seaf\Kernel\Module;

use Seaf\Core\Pattern\ExtendableMethods;

class System extends Module
{
    use ExtendableMethods {
        ExtendableMethods::call as __call;
    }

    public function initModule ( )
    {
        $this->map(array(
            'halt' =>  '_halt'
        ));
    }

    public function _halt ($body = 0)
    {
        exit($body);
    }
}
