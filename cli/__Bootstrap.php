<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

require_once __DIR__.'/../__Bootstrap.php';

if(!defined('SEAF_PROJECT_ROOT')) {
    define('SEAF_PROJECT_ROOT', __DIR__);
}
if(!defined('SEAF_ENV')) {
    define('SEAF_ENV', 'development');
}

// Singletonインスタンスを取得する
Seaf::getSingleton( )->init(SEAF_PROJECT_ROOT, SEAF_ENV);
