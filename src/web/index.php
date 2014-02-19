<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * description of web/src/web/index.php
 *
 * File: web/src/web/index.php
 * Created at: 2月 20, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
require_once dirname(__FILE__).'/../../vendor/autoload.php';

$root_dir = realpath(dirname(__FILE__));
$static_dir = $root_dir."/static";

use Seaf\Seaf;
Seaf::extension(); 

Seaf::enable('env');
Seaf::envSet('dirs.static', $static_dir);
// WEBぺーじを作成する
// 
// これでロードされるもの
//
// - helper 
//  - route('/', function(){} )
// - action
//  - start
//  - halt
Seaf::enable('http');
Seaf::before('renderer', function($args, &$output){
    $output.= '<html>';
    $output.= '<style>body{background: url("/static/assets/styles/images/bg.jpg")}</style>';
    $output.= ' <body><pre>';
});
Seaf::after('renderer', function($args, &$output){
    $output = str_replace('W','VV',$output);
    $output.= ' </pre></body>';
    $output.= '</html>';
});
Seaf::route('/user/@name', function($name ){
    echo '<h1>Hello World!'.$name.'</h1>';
});
Seaf::route('/user/@name(/@age)(/@place)*', function($name, $age, $place ){
    Seaf::renderer('<h1>Hello World!'.$name.'</h1>');
});
Seaf::route('/', function( ){
    Seaf::renderer('<h1>Hello World!</h1>');
});
Seaf::route('/static/@file:*', function( $file, $route ){
    Seaf::staticFile($file);
});

Seaf::start();
