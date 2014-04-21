<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Yahoo;

/**
 * Yahooモジュール
 */
use Seaf;

/**
 * クラスローダを設定する
 */
Seaf::AutoLoader()->addNamespace(
    'Seaf\Module\Yahoo',
    __DIR__.'/lib'
);

/**
 * Seafコンポーネントに設定する
 */
Seaf::register(
    'yahoo',  __NAMESPACE__.'\\Yahoo'
);
