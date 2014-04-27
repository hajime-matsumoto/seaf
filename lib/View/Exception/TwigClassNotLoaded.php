<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\Exception;

class TwigClassNotLoaded extends Exception
{
    public function __construct ( )
    {
        parent::__construct("CLASS NOT FOUND");
    }
}
