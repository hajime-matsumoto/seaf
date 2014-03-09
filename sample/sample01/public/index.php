<?php
require_once __DIR__.'/../bootstrap.php';

use Seaf\Web\Application;
use Seaf\Web\AssetManager;

// ロガーをPHPコンソールにする
//Seaf::logHandler('default', array(
//   'type'=>'phpConsole'
//));
Seaf::logHandler('default', array(
    'type'=>'file',
    'file'=>'/tmp/seaf.log',
    'fileType'=>'a'
));


// WEBアプリケーションの起動
$app = new Application();

// Viewを使う
$app->config()
    ->set('root.path',realpath(__DIR__.'/../'))
    ->set('view.path','./views')
    ->set('cache.path','./tmp/cache')
    ->set('view.enable',1);


// URIルーティングを設定する
$app->route('/', function($req, $res, $app) {
    $app->set('template','index');
    $res->param('title','Hello World');
    echo "AAAAAAAA";
});
$app->route('/twig', function($req, $res, $app) {
    $app->view()->set('engine','twig');
    $app->set('template','index');
});

// アセットを組み込む
$app->route('/assets/*',function ($req, $res, $app) {
    $nextUri=substr($req->getUri(),8);

    $am = new AssetManager();
    $am->addPath(__DIR__.'/../assets');
    $am->request()->setUri($nextUri);
    $am->run();
});

// 実行
$app->run();
