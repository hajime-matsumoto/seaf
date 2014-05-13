<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

require_once __DIR__ . '/../../__Bootstrap.php';

// Web用の設定
BackEnd( )->registerModule([
    'Web' => 'Seaf\Web\WebFacade'
])->on('log', function($e) {
    echo $e->log."\n";
});

// ロギングを有効にする
// Socket:/tmp/logへ飛ばす
BackEnd( )->logHandler( )->addWriter([
    'type'   => 'socket',
    'address' => '/tmp/log',
    'filter' => [
        'level' => 'all ^ debug'
    ],
    'formatter' => [
        'head' => '(%label) [ %tag ] ----',
    ]
]);

BackEnd( )->registry->debugOn();
