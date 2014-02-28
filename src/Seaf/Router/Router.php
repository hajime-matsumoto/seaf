<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Router;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Collection\ArrayCollection;

/**
 * ルータコンポーネント
 */
class Router
{
    private $routes;
    private $mount;

    private $idx = 0;

    public function __construct( )
    {
        $this->init();
    }

    public function init()
    {
        $this->routes = array();
        $this->mount = new ArrayCollection();
    }

    /**
     * マウントする
     *
     * @param string
     * @param object
     */
    public function mount( $path, $app )
    {
        $this->mount->set($path, $app);
    }

    /**
     * ルートを作成する
     *
     * @param string
     * @param callback 
     */
    public function addRoute( $pattern, $callback )
    {
        if (strpos($pattern, ' ') !== false) 
        {
            list($method, $url) = explode( ' ', trim($pattern), 2);
            $methods = explode( '|', $method);
            array_push($this->routes, new Route($url, $callback, $methods));
        }
        else
        {
            array_push($this->routes, new Route($pattern, $callback, array('*')));
        }
    }

    /**
     * ルートを取得する
     */
    public function route( $request )
    {
        $url = $request->getURL();
        // マウントがあるか調べる
        $mount = "";
        foreach( $this->mount as $path=>$app )
        {
            if( strpos($url,$path) === 0 ){
                $app->request()->setBaseURL(
                    rtrim($request->getBaseURL().$path,'/')
                );
                return $app->run();
            }
        }

        for( ; $this->idx<count($this->routes); $this->idx++)
        {
            $route = $this->routes[$this->idx];
            $isMatch = 
                $route->matchMethod($request->getMethod()) &&
                $route->matchURL($request->getURL());
            if( $isMatch )
            {
                return $route;
            }
        }
        return false;
    }

    public function next()
    {
        $this->idx++;
    }

    public function reset()
    {
        $this->idx = 0;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
