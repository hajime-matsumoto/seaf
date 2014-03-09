<?php
require_once __DIR__.'/../../bootstrap.php';

use Seaf\Web\AssetManager;

// ロガーをPHPコンソールにする
Seaf::logHandler('default', array(
    'type'=>'file',
    'file'=>'/tmp/asset.log',
    'fileType'=>'w'
));


// アセットマネージャ
$am = new AssetManager();
$am->addPath(__DIR__.'/../../assets');

// 実行
$am->run();
