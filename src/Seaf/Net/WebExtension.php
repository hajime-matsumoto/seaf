<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * ウェブエクステンションクラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Net;

use Seaf\Core\Extension;
use Seaf\Core\Base;

/**
 * WEBエクステンションクラス
 *
 * レジストされるコンポーネント
 *
 * @component request Seaf\Net\Request
 * @component response Seaf\Net\Response
 * @component router Seaf\Net\Router
 */
class WebExtension extends Extension
{
    /**
     * エクステンションを初期化する
     */
    public function initExtension( )
    {
    }

    /**
     * URLパターンを登録する。
     *
     * @param string $pattern
     * @param callback $pattern
     * @bind route
     * @usePrefix false
     */
    public function actionRoute( $pattern, $func = null )
    {
        if( $func == null && is_array($route) )
        {
            foreach( $route as $k => $v )
            {
                $this->actionRoute( $k, $v );
            }
        }
        else
        {
            $this->router->map($pattern, $func);
        }
    }

    public function actionMap( $patterm, $func )
    {
        $this->comp('router')->map( $patterm, $func );
    }


    /**
     * Webフレームワークをスタートする
     *
     * @bind start
     * @usePrefix false
     */
    public function actionStart( )
    {
        $dispatched = false;
        $req    = $this->request;
        $res    = $this->response;
        $router = $this->router;

        if( ob_get_length() > 0 )
        {
            $res->write(ob_get_clean());
        }

        ob_start();

        $this->after('start', function() {
            $this->stop();
        });

        // Route the request
        while ($route = $router->route($req)) 
        {
            $params = array_values($route->params);
            array_push($params, $route);

            $continue = call_user_func_array(
                $route->callback,
                $params
            );

            $dispatched = true;

            if (!$continue) break;

            $router->next();
        }

        if (!$dispatched) {
            $this->act('notFound');
        }
    }

    /**
     * Web処理を終了する
     *
     * @bind stop
     * @usePrefix false
     */
    public function actionStop( $code = 200 )
    {
        // for phpunit issu
        if( ob_get_length() == 0 )
        {
            ob_start();
        }
        $this->response
            ->status( $code )
            ->write( ob_get_clean() )
            ->send( );
    }

    /**
     * 404表示をする
     *
     * @bind notFound
     * @usePrefix false
     */
    public function actionNotFound( )
    {
        $this->response
            ->status(404)
            ->write(
                '<h1>404 Not Found</h1>'.
                '<section style="padding:10px">URL:'.$this->request->url.'</section>'
                .str_repeat(' ', 512)
            )->send();
    }

    /**
     * 強制終了
     *
     * @bind notFound
     * @usePrefix false
     */
    public function actionHalt( $message, $code = 200 )
    {
        $this->response
            ->status( $code )
            ->write( $message )
            ->send( );
    }
}
