<?php
use Seaf\Core\Kernel;

Kernel::init();

// ファイルパスを追加
Kernel::fs()->addFilePath('/core',__DIR__);

// ネームスペースをオートローダに登録
Kernel::cl()->addNamespace('Seaf\Core','/core');
