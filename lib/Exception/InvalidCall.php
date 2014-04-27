<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Exception;

class InvalidCall extends Exception
{
    public function __construct($name, $params, $target)
    {
        parent::__construct(sprintf(
            'Invalid Call %s->%s %s',
            get_class($target),
            $name,
            print_r($params, 1)
        ));
    }
}
