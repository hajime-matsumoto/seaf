<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\Exception;

class ViewFileNotFound extends Exception
{
    public function __construct ($file, $dirs)
    {
        parent::__construct($file.' in '.print_r($dirs,1));
    }
}
