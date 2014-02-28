<?php
require_once '../../../vendor/autoload.php';

use Seaf\Seaf;
use Seaf\Http\AssetManager;

// ロガーをセット
Seaf::logger()->addHandler(array('type'=>'PHPConsole'));
Seaf::logger()->register();

// デバッガをセット
Seaf::debugger()->register();
function d($var) {
    Seaf::d($var);
}

// -----------------------
// WebAppを作成
// -----------------------
class App extends \Seaf\Http\WebApp
{
    public function __construct( )
    {
        parent::__construct();

        // フィルター
        $this->event()->addHook('after.start',function(){
            $c = ob_get_clean();
            ob_start();
            echo str_replace('h','<link href="/web/assets/style.css" rel="stylesheet">Haaaaaaaaaaa', $c);
        });

        // セキュリティチェックとか
        $this->event()->addHook('before.start',function(){
            $url = $this->request()->getURL();

            if( strpos($url,'/secure') === 0 )
            {
                $this->halt(500, 'セキュリティエラー');
            }

        });

        // メインコンテンツ
        $this->router()->addRoute('GET /(@page:*)',function(){
            echo 'hi';
        });
    }
}

// アセットマネージャを起動
$am = new AssetManager();
$am->addPath(dirname(__FILE__).'/../assets');

// アセットマネージャを "/assets" にマウント
Seaf::http()->router()->mount( '/assets', $am);

// WebAppを "/" にマウント
Seaf::http()->router()->mount( '/', new App( ));



// HTTPフレームワークを起動
Seaf::http()->run();
