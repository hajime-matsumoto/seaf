<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\P18n;

use Seaf;

/**
 * クラスローダを設定する
 */
Seaf::autoLoader()->addNamespace(
    'Seaf\Module\P18n',
    __DIR__
);

/**
 * Seafコンポーネントに設定する
 */
Seaf::register(
    'p18n',
    function ( ) {
        return P18n::factory();
    }
);
