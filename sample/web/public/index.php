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
            echo $c;
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
?>
<!DOCTYPE html>
<head>
<link href="/web/assets/layout.css" rel="stylesheet"/>
</head>
<body>

<div class="row">
    <div class="box span3">1</div>
    <div class="box span3">2</div>
    <div class="box span3">3</div>
    <div class="box span3">4</div>
</div>
<div class="row">
    <div class="box span9">5</div>
    <div class="box span3">6</div>
</div>
<div class="row">
    <div class="box span3">7</div>
    <div class="box span9">8
        <div class="row">
            <div class="box span1">9</div>
            <div class="box span8">0</div>
        </div>
    </div>
</div>

<article>
<header>HEADER</header>
<section>メイン</section>
<aside>メニュー</aside>
<footer>フッター</footer>
</article>

</body>

<?php
        });
    }
}

// アセットマネージャを起動
$am = new AssetManager( array(
    'paths'=>array(
        dirname(__FILE__).'/../assets'
    )
));


// アセットマネージャを "/assets" にマウント
Seaf::http()->router()->mount( '/assets', $am);

// WebAppを "/" にマウント
Seaf::http()->router()->mount( '/', new App( ));



// HTTPフレームワークを起動
Seaf::http()->run();
