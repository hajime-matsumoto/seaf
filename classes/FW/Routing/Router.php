<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Routing;

use Seaf\Request\Request;

/**
 * ルーター
 */
class Router
{
    private $routes = array();
    private $idx = 0;

    public function __construct ( )
    {
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

        $this->routes[] = $this->createRoute($pattern, $command);
        return $this;
    }

    /**
     * ルートオブジェクトを作成する
     */
    protected function createRoute($pattern, $command)
    {
        return new Route($pattern, $command);
    }

    /**
     * ルートを検索する
     */
    public function route (Request $request)
    {
        while(isset($this->routes[$this->idx])) {

            $current = $this->routes[$this->idx];

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
