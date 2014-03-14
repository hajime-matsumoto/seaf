<?php
require_once __DIR__.'/../src/__bootstrap.php';

use Seaf\Seaf;
//
// --------------------------
// 初期化処理
// --------------------------
//

if (!defined('SEAF_PROJECT_ROOT')) {
    define('SEAF_PROJECT_ROOT',__DIR__);
}
Seaf::init(SEAF_PROJECT_ROOT.'/etc/setting.yaml');
