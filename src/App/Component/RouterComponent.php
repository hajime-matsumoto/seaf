<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\App\Component;

use Seaf\Core\Environment;

/**
 * ルーターコンポーネント
 */
class RouterComponent
{
    private $routes = array();
    private $idx = 0;

    /**
     * @var Environment
     */
    private $env;

    public function initComponent (Environment $env) 
    {
        $this->env = $env;
        $this->routes = array();
    }

    /**
     * ルートを登録する
     */
    public function map ($pattern, $command = null)
    {
        if (is_array($pattern)) {
            foreach ($pattern as $k=>$v) {
                $this->map($k,$v);
            }
            return $this;
        }

        if (is_string($command) && !is_callable($command)) {
            $command = array($this->env,$command);
        }

        $this->routes[] = new Route($pattern, $command);
        return $this;
    }

    /**
     * ルートを検索する
     */
    public function route (RequestComponent $request)
    {
        $uri    = $request->uri();
        $method = $request->method();
        $routes = $this->routes;

        while(isset($routes[$this->idx])) {

            $current = $routes[$this->idx];

            if ( $current->match($request) ) {
                return $current;
            }

            $this->next();
        }
    }

    public function next ( )
    {
        $this->idx++;
    }

}