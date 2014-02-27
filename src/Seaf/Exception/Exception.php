<?php

namespace Seaf\Exception;

use Exception as PHPException;

class Exception extends PHPException
{
    public function __construct( $message )
    {
        if( func_num_args() > 0 )
        {
            $message = vsprintf( $message, array_slice( func_get_args(), 1 ) );
        }

        parent::__construct( $message );
    }
}
