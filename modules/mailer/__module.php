<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Mailer;

use Seaf;

Seaf::autoLoader()->addNamespace(
    'Seaf\Module\Mailer',
    __DIR__
);

Seaf::register(
    'mailer',
    function ( ) {
        $c = Seaf::config('module.configs.mailer', array());
        return Mailer::factory($c);
    }
);
