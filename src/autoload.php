<?php
/**
 * Seaf Auto Loader
 */

// Seafを読み込む
require_once dirname(__FILE__).'/Seaf.php';

// オートローダクラスを読み込む
require_once dirname(__FILE__).'/bundle/core/src/Core/Loader/AutoLoader.php';

// Coreのオートロードよ読み込む
require_once dirname(__FILE__).'/bundle/core/src/autoload.php';

/**
 * バンドル配下のautoload.phpを全て読み込む
 */
foreach ( glob(dirname(__FILE__).'/bundle/*/src/autoload.php') as $file ) {
    require_once $file;
}
