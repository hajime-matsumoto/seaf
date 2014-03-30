<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Kvs;

use Seaf;

Seaf::autoLoader()->addNamespace(
    'Seaf\Module\Kvs',
    __DIR__
);

Seaf::register(
    'kvs',
    function ( ) {
        $c = Seaf::config('kvs', array());
        return Module::factory($c);
    }
);
