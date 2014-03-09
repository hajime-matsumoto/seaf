<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Commander;

use Seaf\Environment\Environment;

/**
 * コマンダー
 * ====================
 *
 * 関連クラス
 * --------------------
 * Router : ルーティング担当
 * Response : レスポンスの管理
 * Request : リクエストの管理
 */
class Commander extends Environment
{
    private $map = array();

    private $routes;

    /**
     * 
     */
    public function __construct ( )
    {
        parent::__construct( );

        $this->bind($this, $this->map);

        // オートバインディング
        // _xxx to map(xxx,_xxx)
        // ちょっと危険か、、、
        foreach (get_class_methods($this) as $m) {
            if ($m{0} == '_' && $m{1} !='_') {
                $this->map(substr($m,1), $m);
                $this->map[substr($m,1)] = $m;
            }
        }
    }

    /**
     * コマンドパターンを登録する
     */
    public function _route ($pattern, $command)
    {
        $this->routes = array($pattern, $command);
    }

    /**
     * コマンドを実行する
     */
    public function _run (Request $request = null)
    {
        $executed = false;

        while( $route = $this->router()->route($request)) {

            $isContinue = $this->execute($route);

            if ($isContinue !== true) break;

            $executed = true;
        }

        if (!$executed) {

            // 実行できなかった事を通知
            $this->trigger('notExecuted', $request);
        }
    }

    public function _execute (Route $route) {
        // ルートの実行

    }

    public function _render ( )
    {
        echo 'コマンドを実行したよ';
    }

    /**
     *
     */
    public function __call($name, $params)
    {
        if (array_key_exists($name, $this->map)) {
            $this->event()->trigger('before.'.$name);
            $return = parent::__call($name, $params);
            $this->event()->trigger('after.'.$name, $return);
            return $return;
        }

        return parent::__call($name, $params);
    }
}
