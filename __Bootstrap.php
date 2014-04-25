<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

require_once __DIR__.'/lib/Loader/ClassLoader.php';

// ローダーを設定
$loader = Seaf\Loader\ClassLoader::factory( )
    ->addNamespace('Seaf', __DIR__.'/lib')
    ->register( );
