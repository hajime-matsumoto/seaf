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

use Seaf\Seaf;
Seaf::extension(); 

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
Seaf::before('start', function( ){
    echo '<html>';
    echo ' <body>';
});
Seaf::after('start', function( ){
    echo ' </body>';
    echo '</html>';
});
Seaf::route('/user/@name', function($name ){
    echo '<h1>Hello World!'.$name.'</h1>';
});
Seaf::route('/user/@name(/@age)(/@place)*', function($name, $age, $place ){
    echo '<h1>Hello World!'.$name.'</h1>';
});
Seaf::route('/', function( ){
    echo '<h1>Hello World!</h1>';
});

Seaf::start();
