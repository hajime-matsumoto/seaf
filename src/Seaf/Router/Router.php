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
     * @param mixed $pattern 配列で複数渡せる
     * @param callback 
     */
    public function map( $pattern, $callback = null )
    {
        if( is_array($pattern) && $callback == null )
        {
            foreach( $pattern as $k => $v )
            {
                $this->map( $k, $v );
            }
            return;
        }

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

        // マウントがあるか調べる
        $mount = "";
        foreach( $this->mount as $path=>$app )
        {
            if( strpos($url,$path) === 0 ){
                $app->request()->setBaseURL(
                    $request->getBaseURL() == '/' ? $path: $request->getBaseURL().$path
                );
                return $app->run();
            }
        }

        return false;
    }

    /**
     * ルートをひとつ進める
     */
    public function next()
    {
        $this->idx++;
    }

    /**
     * ルートインデックスを初期化
     */
    public function reset()
    {
        $this->idx = 0;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
