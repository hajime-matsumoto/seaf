<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB;

use Seaf;

Seaf::autoLoader()->addNamespace(
    'Seaf\Module\DB',
    __DIR__
);

Seaf::register(
    'DB',
    function ( ) {
        $c = Seaf::config('db', array());
        return Module::factory($c);
    }
);
