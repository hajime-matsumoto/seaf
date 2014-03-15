<?php
require_once __DIR__.'/../src/__bootstrap.php';

use Seaf\Seaf;
use Seaf\Kernel\Kernel;

//
// --------------------------
// 初期化処理
// --------------------------
//

if (!defined('SEAF_PROJECT_ROOT')) {
    define('SEAF_PROJECT_ROOT',__DIR__);
}
if (!defined('SEAF_ENV')) {
    define('SEAF_ENV', 'development');
}
Seaf::init(SEAF_PROJECT_ROOT.'/etc/setting.yaml', $env = SEAF_ENV);

// テスト用のクラスを登録
Kernel::autoLoader()->addNamespace('SeafTest',__DIR__.'/class');
