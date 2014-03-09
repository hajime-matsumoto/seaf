<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\FrameWork;
use Seaf\Commander\Command;
use Seaf\Helper\ArrayHelper;

/**
 * Application
 * ================================
 *
 * 使い方
 * <code>
 * $app->request(); # リクエストを取得
 * $app->response(); # レスポンスを取得
 * $app->route(<パターン>,<コールバック>); # URIルーティング
 * </code>
 */
class Application extends FrameWork\Application
{
    /**
     * 初期処理
     */
    public function initApplication ( )
    {
        parent::initApplication( );

        $server = ArrayHelper::init($_SERVER);

        // ベースURLを求める
        if (php_sapi_name() == 'cli-server') {
            $this->set('base.uri', '');
        } elseif ($server->get('SCRIPT_NAME') == $server->get('REQUEST_URI')) {
            $this->set('base.uri', $server->get('SCRIPT_NAME'));
        } elseif ($server->get('PATH_INFO', false) != false) {
            $this->set('base.uri',$server->get('SCRIPT_NAME'));
        } else {
            $this->set('base.uri',dirname($server->get('SCRIPT_NAME')));
        }

        // アセットマネージャを登録する
        $this->register('assetManager','Seaf\Web\AssetManager',array(),function ($am) {
            $am->register('config',$this->config());
        });
    }

    /**
     * 実行
     */
    public function run ( )
    {
        $req      = $this->request();
        $res      = $this->response();
        $rt       = $this->router();
        $executed = false;

        if ($this->getConfig('view.enable') == true) {

            $this->on('pre.run',function(){
                ob_start();
            });

            $this->on('post.run',function(){
                $contents = ob_get_clean();
                $this->render($contents);
            });
        }


        $this->trigger('pre.run', $req, $res, $this);

        // ディスパッチループ処理
        while ( $route = $rt->route($req) )
        {
            $isContinue = $route->getCommand()->execute($req, $res, $this);

            if ($isContinue == false) {
                $executed = true;
                break;
            }

            $this->router()->next();
        }

        // 何もヒットしなければnotfoundへ
        if ($executed == false) {
            $this->trigger('notfound', $req, $res, $this);
            $this->notfound();
        }

        $this->trigger('post.run', $req, $res, $this);
    }

    /**
     * config()->get('view.enable') の値がTrue評価であれば
     * ディスパッチループの後にこのメソッドが呼ばれる
     */
    public function render($contents = null)
    {
        $view = $this->get('template');

        $array = $this->response()->toArray();
        $params = $array['params'];
        $params['contents'] = $contents;

        echo $this->view()->render($view, $params);
    }

    /**
     * リダイレクト処理
     */
    public function redirect($uri, $code = 303)
    {
        $this->response( )
            ->status($code)
            ->header('Location', $this->get('base.uri').'/'.ltrim($uri,'/'))
            ->send( );
    }
}
