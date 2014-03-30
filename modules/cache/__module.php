<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Cache;

use Seaf;

Seaf::enmod('kvs');

Seaf::autoLoader()->addNamespace(
    'Seaf\Module\Cache',
    __DIR__
);

Seaf::register(
    'cache',
    function ( ) {
        $c = Seaf::config('cache', array());
        return Module::factory($c);
    }
);
