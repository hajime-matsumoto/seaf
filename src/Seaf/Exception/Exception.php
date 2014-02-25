<?php

namespace Seaf\Exception;

use Seaf\Core\Exception\Exception as CoreException;

class Exception extends CoreException
{
    public function __construct( )
    {
        $args = func_get_args();
        /*
        ob_start();
        \Seaf\Seaf::report();
        $report = ob_get_clean();
        array_push($args, $report);
         */
        call_user_func_array(array('parent','__construct'), $args);
    }
}
