<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * description of src/Seaf/HTTP/Http.php
 *
 * File: src/Seaf/HTTP/Http.php
 * Created at: 2月 19, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Extension\HTTP;

use Seaf\Extension\Extension;

/**
 * short description of Http
 *
 * long description of Http
 */
class Http extends Extension
{
    private $base;

    /**
     * short description of enable
     *
     * description of enable
     *
     * @params 
     * return null
     */
    public function enable($base) 
    {
        $this->base = $base;

        parent::enable($base);
        $this->initialize($base);
        return null;
    }

    /**
     * short description of initialize
     *
     * description of initialize
     *
     * @params 
     * return $this;
     */
    protected function initialize($base) 
    {
        // モジュールをロードする
        $base->register('router', 'Seaf\Extension\HTTP\Router');
        $base->register('request', 'Seaf\Extension\HTTP\Request');
        $base->register('response', 'Seaf\Extension\HTTP\Response');

        // ヘルパーをマップする
        $base->setHelpers(array(
            'route' => 'map'
        ), $this);

        // Actions
        $base->setActions(array(
            'start' => 'start',
            'stop' => 'stop',
            'notFound' => 'notFound',
            'staticFile' => 'staticFile',
            'renderer' => 'renderer'
        ), $this);
        return $this;

        // Module
        $self = $this;
    }


    public function map( $pattern, $callback )
    {
        $this->base->router()->map( $pattern, $callback);
    }

    public function renderer($documents)
    {
        $base = $this->base;
        $base->after('renderer', function($params, &$output) use($base, $documents){
            echo $output;
        });
        return $documents;
    }

    public function start( ) {
        $base = $this->base;
        $req = $base->request();
        $res = $base->response();
        $router = $base->router();

        if (ob_get_length() > 0) {
            $res->write(ob_get_clean());
        }
        ob_start();

        $base->after('start', function() use($base){ $base->stop(); });

        while ( $route = $router->route($req) ) {
            $params = array_values( $route->params );
            array_push( $params, $route );
            $continue = $base->execute( $route->callback, $params );
            $dispatched = true;
            if(!$continue) break;
            $router->next();
        }

        if(!$dispatched) {
            $base->notFound();
        }
    }

    public function stop( $code = 200) {

        $this->base->response()->status($code)->write(ob_get_clean())->send();
    }

    public function notFound( ){
        $this->base->response()
            ->status(404)
            ->write( '<h1>404 Not Found</h1>'. str_repeat(' ', 512))
            ->send();
    }

    public function staticFile( $filepath) {
        $dirpath = $this->base->envGet("dirs.static", "./");
        $filepath= $dirpath.'/'.$filepath;

        if( !file_exists($filepath) ) $this->base->notFound();
        $this->base->response()
            ->status(200)
            ->header('Content-Type', mime_content_type($filepath) )
            ->write(file_get_contents($filepath))
            ->send();
    }

} 
