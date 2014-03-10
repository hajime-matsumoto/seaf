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

        // @todo 全体的にリクエストの初期処理 $this->initRequestとかにしてしまおうか
        // ベースURLを求める
        // @todo これってRequestに仕事させてもいいと思う
        if (php_sapi_name() == 'cli-server') { // cli-serverだったらBaseURIは不要
            $this->set('base.uri', '');
        } elseif ($server->get('SCRIPT_NAME') == $server->get('REQUEST_URI')) {  // ScriptNameとRequestURIが完全一致した場合も不要
            $this->set('base.uri', $server->get('SCRIPT_NAME'));
        } elseif ($server->get('PATH_INFO', false) != false) { // パスインフォを使っている場合はindex.phpまでがベースになるはず
            $this->set('base.uri',$server->get('SCRIPT_NAME'));
        } else { // 普段は？
            $this->set('base.uri',dirname($server->get('SCRIPT_NAME')));
        }

        // アセットマネージャを登録する
        $this->register('assetManager','Seaf\Web\AssetManager',array(),function ($am) {
            // コンフィグを引き継ぐ
            $am->register('config',$this->config());
        });


        $this->on(array(
            'pre.run'  => '_preRunHook',
            'post.run' => '_postRunHook'
        ));
    }


    /**
     * Runの実行開始前に実行される
     *
     * @param Request
     * @param Response
     * @param Application
     */
    public function _preRunHook ($req, $res, $app) 
    {
        $this->debug(sprintf(
            "ACCESS: %s %s; BASEURI: %s",
            $req->getMethod(),
            $req->getUri(),
            $this->get('base.uri')
        ));

        // Viewを使うなら出力をバッファリングする
        if ($this->getConfig('view.enable') == true) {
            ob_start();
        }
    }

    /**
     * Runの実行後に実行される
     *
     * @param Request
     * @param Response
     * @param Application
     */
    public function _postRunHook ($req, $res, $app) 
    {
        // Viewを使っていた場合出力バッファを取得し
        // 描画メソッドを実行
        if ($this->getConfig('view.enable') == true) {
            $contents = ob_get_clean();
            $this->render($contents);
        }
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

        $this->response()
            ->status(200)
            ->write($this->view()->render($view, $params))
            ->send();
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
