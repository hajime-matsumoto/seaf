<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork\Component;

use Seaf\FrameWork\Application;

/**
 * Router
 */
class Router
{
    private $app;
    private $routes;
    private $route_index = 0;

    public function __construct (Application $app) 
    {
        $this->app = $app;
        $this->route_index = 0;
    }

    public function map ( $pattern, $action )
    {
        if (is_string($action)) {
            $action = array($this->app, $action);
        }
        $this->routes[] = new Route($pattern, $action);

        $this->app->debug($pattern."をマップしました");

        return $this;
    }

    public function route ( $request )
    {
        if (is_string($request)) {
            $request = new Request($this->app);
            $request->setUri($request);
        }

        $app    = $this->app;
        $uri    = $request->getUri();
        $method = $request->getMethod();
        $routes = $this->routes;
        $offset = $this->route_index;

        $this->app->debug($uri."で一致するルートを探します");

        while(isset($routes[$offset])) {

            $current = $routes[$offset];

            if ( $current->match($request) ) {
                return $current;
            }

            $offset = $this->next();
        }
    }

    public function next ( )
    {
        $offset = $this->route_index;
        $next = $offset+1;
        $this->route_index = $next;
        return $next;
    }
}
