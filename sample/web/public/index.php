<?php
// ------------------------------
// 初期処理
// ------------------------------

// アプリケーションルートを定義
define('APP_ROOT', realpath(dirname(__FILE__).'/..'));

// Seafのオートローダを使う
require_once realpath(APP_ROOT.'/../../src/autoload.php');

use Seaf\Seaf;
use Seaf\Http\AssetManager;

// アプリケーション環境
define('APP_ENV', 'development');

// ------------------------------
// アプリケーションの設定
// ------------------------------
Seaf::registry()->set(array(
    'app.root'   => APP_ROOT,
    'app.env'    => APP_ENV,
    'cache.path' => '{{app.root}}/tmp/cache',
    'view.path'  => '{{app.root}}/views'
));

// ロガーの設定
Seaf::logger()->addHandler(
    array(
        'type'=>'PHPConsole'
    )
);

// ------------------------------
// アセットマネージャの設定
// ------------------------------
$am = new AssetManager( );
$am->addPath(
    Seaf::registry()->get('app.root').'/assets'
);
// アセットマネージャを "URL /assets" にマウントする
Seaf::http()->router()->mount( '/assets', $am);

// ------------------------------
// viewが使用するパラメタを定義する
// ------------------------------
Seaf::view()->setParam('base_url',Seaf::http()->request()->getBaseURL());

// ------------------------------
// URLの設定
// ------------------------------
Seaf::http()->router()->map('/',function( ){
    echo Seaf::view()->render('index.twig');
});


// ------------------------------
// HTTPフレームワークの実行
// ------------------------------
Seaf::http()->run();
















