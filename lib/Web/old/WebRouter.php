<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\Base\Event;

/**
 * ルータクラス
 */
class WebRouter implements Event\ObservableIF
{
    use Event\ObservableTrait;

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
    public function __construct ( )
    {
        $this->routes = [];
        $this->idx    = 0;
    }

    /**
     * ルートを検索する
     */
    public function route (WebRequest $request)
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

        $this->routes[] = new WebRoute($pattern, $action);
        return $this;
    }


    public function next ( )
    {
        $this->idx++;
    }
}
