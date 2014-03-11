<?php
/**
 * カーネルを読み込む
 */
require_once __DIR__.'/Core/Kernel.php';

use Seaf\Core\Kernel;

// カーネルをイニシャライズ
$kernel = Kernel::init();

// ファイルシステムをマウント
$kernel->fileSystem()->addFilePath(
    '/SeafRoot',
    __DIR__
);

// オートロードを登録
$kernel->classLoader()->addNamespace(
    'Seaf',
    '/SeafRoot'
);

/**
 * Seafを読み込む
 */
require_once __DIR__.'/Seaf.php';
