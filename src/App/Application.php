<?php

namespace Seaf\App;

use Seaf\Core\Environment;

use Seaf\App\Component\RequestComponent as Request;
use Seaf\App\Component\ResponseComponent as Response;
use Seaf\App\Component\RouterComponent as Router;
use Seaf\App\Component\Route;

/**
 * アプリケーションクラス
 * ===============================
 *
 * ここで追加されるコンポーネント
 * -------------------------------
 * * Request
 * * Response
 * * Router
 * * Event
 */
class Application extends Environment
{
    public function initEnvironment ( )
    {
        parent::initEnvironment();
        $this->initApplication();
    }
    
    public function initApplication ( )
    {
        $this->bind('event', array(
            'on' => 'on',
            'off' => 'off',
            'trigger' => 'trigger'
        ));

        $this->bind($this,array(
            'run'      => '_run',
            'route'    => '_route',
            'execute'  => '_execute',
            'notfound' => '_notfound'
        ));
    }

    /**
     * ルートを設定する
     *
     * @param string
     * @param mixed
     */
    public function _route ($pattern, $command)
    {
        // ルータを作成する
        $this->router()->map($pattern, $command);
    }

    /**
     * アプリケーションを実行する
     *
     * @return void
     */
    public function _run (Request $req = null, Response $res = null, Router $rt = null)
    {
        if ($req == null) $req = $this->request();
        if ($res == null) $res = $this->response();
        if ($rt == null) $rt  = $this->router();

        $this->trigger('before.run', $req, $res, $rt);
        $isMatch = false;
        while ($route = $rt->route($req)) {
            $this->trigger('before.execute', $req, $res, $route);
            $isMatch = $this->execute($route, $req, $res, $this);
            $this->trigger('after.execute', $req, $res, $route);

            if ($isMatch !== false) break;
        }

        if ($isMatch === false) {
            $this->notfound();
        }
        $this->trigger('after.run', $req, $res, $this);
    }

    /**
     * ヒットしたコマンドを実行する
     *
     * @param Route $route
     * @param Request $req
     * @param Response $res
     * @param App $app
     */
    public function _execute (Route $route, Request $req, Response $res, Application $app)
    {
        return $route->execute($req, $res, $app);
    }
}
