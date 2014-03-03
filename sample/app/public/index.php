<?php
/**
 * Executer
 */
require_once dirname(__FILE__).'/../../../vendor/autoload.php';
use Seaf\Seaf;
use Seaf\Http\AssetManager;

Seaf::system()->setLang('ja');
Seaf::logger()->addHandler(array('type'=>'PHPConsole'));

// アプリケーションルートを定義
define('APP_ROOT', realpath(dirname(__FILE__).'/..'));
define('APP_ENV', 'development');

Seaf::registry()->set(array(
    'app.root'   => APP_ROOT,
    'app.env'    => APP_ENV,
    'cache.path' => '/tmp/cache',
    'view.path'  => '/views'
));

$am = new AssetManager( );
$am->addPath(
    Seaf::registry()->get('app.root').'/assets' // アセットファイル置き場
);

// アセットマネージャを "URL /assets" にマウントする
Seaf::http()->router()->mount( '/assets', $am);

require_once '../app.php';
require_once '../admin.php';
$app = new App( );

/* ----- Config ---------*/
$app->registry()->set('admin.mail', "mail@hazime.org");

/* ----- Run ---------*/
Seaf::http()->router()->mount('/admin', new Admin());
Seaf::http()->router()->mount('/', $app);
Seaf::http()->run();
