<?php
/**
 * カーネルを読み込む
 */
require_once __DIR__.'/Kernel/Kernel.php';
require_once __DIR__.'/Seaf.php';


//
// --------------------------
// 初期化処理
// --------------------------
//
Seaf::init(__DIR__.'/etc/setting.yaml');
