<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\Exception;

class ViewMethodNotFound extends Exception
{
    public function __construct ($method)
    {
        parent::__construct("METHOD: ".$method);
    }
}
