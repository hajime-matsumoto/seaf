<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Component\Exception;

class ComponentNotFound extends Exception
{
    public function __construct ($component_name, $object)
    {
        parent::__construct (
            'Component ' .
            $component_name .
            ' Not Found From '.
            get_class($object)
        );
    }
}
