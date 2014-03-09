<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork\Component;

use Seaf\FrameWork\Application;

/**
 * Router
 */
class Router
{
    private $app;

    public function __construct (Application $app) 
    {
        $this->app = $app;
        $app->set('router.index.routes', 0);
    }

    public function map ( $pattern, $action )
    {
        if (is_string($action)) {
            $action = array($this->app, $action);
        }
        $this->app->push('routes', new Route($pattern, $action));

        $this->app->debug($pattern."をマップしました");
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
        $routes = $app->get('routes');
        $offset = $app->get('router.index.routes');

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
        $offset = $this->app->get('router.index.routes');
        $next = $offset+1;
        $this->app->set('router.index.routes', $next);
        return $next;
    }
}
