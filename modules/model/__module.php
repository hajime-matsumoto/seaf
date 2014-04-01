<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Model;

use Seaf;

Seaf::autoLoader()->addNamespace(
    'Seaf\Module\Model',
    __DIR__
);

/**
Seaf::register(
    'DB',
    function ( ) {
        $c = Seaf::config('db', array());
        return Module::factory($c);
    }
);
**/
