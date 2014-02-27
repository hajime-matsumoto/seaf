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
use Seaf\Util\DispatchHelper;

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
    public function initializeExtension( $prefix, $env )
    {
        parent::initializeExtension( $prefix, $env);

        $base = $env->get('base');

        // 基本的なオブジェクトを登録する
        $base->register(
            $this->prefix("router"), 'Seaf\Router\Router'
        );
        $base->register(
            $this->prefix("request"), 'Seaf\Request\Request'
        );
        $base->register(
            $this->prefix("response"), 'Seaf\Net\Response'
        );
    }

    /**
     * URLパターンを登録する。
     *
     * @param string $pattern
     * @param callback $pattern
     * @SeafBind route
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


    /**
     * Webフレームワークをスタートする
     *
     * @SeafBind start
     */
    public function actionStart( )
    {
        $dispatched = false;
        $req    = $this->request;
        $res    = $this->response;
        $router = $this->router;
        $self = $this;

        // ルータを巻き戻す
        $router->reset();

        if( ob_get_length() > 0 )
        {
            $res->write(ob_get_clean());
        }

        ob_start();

        $this->on('start.after', function() use ($self){
            $self->stop();
        });

        $this->trigger('start.before');

        // Route the request
        while ($route = $router->route($req)) 
        {
            $params = array_values($route->params);
            array_push($params, $route);

            $continue = DispatchHelper::invokeArgs(
                $route->callback,
                $params
            );

            $dispatched = true;

            if (!$continue) break;

            $router->next();
        }

        if (!$dispatched) {
            $this->notFound();
        }

        $this->trigger('start.after');
    }

    /**
     * Web処理を終了する
     *
     * @SeafBind stop
     */
    public function actionStop( $code = 200 )
    {
        $this->trigger('stop.before');

        $this->response
            ->status( $code )
            ->write( ob_get_clean() )
            ->send( );
    }

    /**
     * 404表示をする
     *
     * @SeafBind notFound
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
     * @SeafBind halt
     */
    public function actionHalt( $message, $code = 200 )
    {
        $this->response
            ->status( $code )
            ->write( $message )
            ->send( );
    }

    /**
     * リダイレクト
     *
     * @SeafBind redirect
     */
    public function actionRedirect( $url, $code = 303)
    {
        $base = $this->request->base;
        $url = rtrim($base,'/').'/'.ltrim($url,'/');

        $this->response
            ->status( $code )
            ->header('Location', $url)
            ->write($url)
            ->send();
    }
}
