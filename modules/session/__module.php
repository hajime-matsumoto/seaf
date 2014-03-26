<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Session;

use Seaf;

Seaf::autoLoader()->addNamespace(
    'Seaf\Module\Session',
    __DIR__
);

Seaf::register(
    'session',
    function ( ) {
        $c = Seaf::config('module.configs.session', array());
        return Session::factory($c);
    }
);
