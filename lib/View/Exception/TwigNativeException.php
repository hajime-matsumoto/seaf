<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View\Exception;

class TwigNativeException extends Exception
{
    public function __construct (\Exception $e)
    {
        parent::__construct(get_class($e).':'.$e->getMessage());
    }
}
