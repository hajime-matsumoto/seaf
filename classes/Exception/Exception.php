<?php

namespace Seaf\Exception;

class Exception extends \Exception 
{

    /**
     * __construct
     *
     * @param $messagea
     * @return void
     */
    public function __construct ($message)
    {
        if (is_array($message)) {
            $format = array_shift($message);
            $message=vsprintf($format,$message);
        }
        parent::__construct($message);
    }
}
