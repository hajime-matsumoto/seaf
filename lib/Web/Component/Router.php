<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\Base\Event;
use Seaf\Web;

/**
 * ルータクラス
 */
class Router implements Web\ComponentIF
{
    use Web\ComponentTrait;

    protected static $object_name = 'Router';

    /**
     * @var array
     */
    private $routes;

    /**
     * @var int
     */
    private $idx;

    /**
     * コンストラクタ
     */
    public function __construct ($module = null)
    {
        if ($module) $this->setParentWebComponent($module);

        $this->routes = [];
        $this->idx    = 0;
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

    /**
     * マップする
     */
    public function map ($pattern, $action) 
    {
        if (is_array($pattern)) {
            foreach ($pattern as $k=>$v) {
                $this->map($k,$v);
            }
            return $this;
        }

        $this->routes[] = new Route($pattern, $action);
        return $this;
    }


    public function next ( )
    {
        $this->idx++;
    }
}
