<?php
/**
 * P18nのオートローダ
 */
use Seaf\Kernel\Kernel;

// ネームスペースの登録
Kernel::autoLoader()->addNamespace(
    'Seaf\Module\P18n',
    __DIR__
);

// グローバルのDIに登録する
Kernel::DI()->register('p18n', 'Seaf\Module\P18n\P18n', null, function($p18n){
    $p18n->register();
});
