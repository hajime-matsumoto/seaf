<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork;

use Seaf\Environment\Environment;
use Seaf\Commander\Command;

/**
 * アプリケーションクラス
 * =================================
 *
 * アプリケーションのひな形になるクラス
 *
 * コンポーネント
 * ---------------------------------
 * - Router
 * - Request
 * - Response
 */
class Application extends Environment
{
    /**
     * 初期化処理
     */
    public function init ( ) {
        parent::init();

        // Application専用のイニシャライザ
        $this->initApplication();
    }

    /**
     * Application専用のイニシャライザ
     */
    public function initApplication( ) 
    {
        // 初期処理を記述する
    }

    /**
     * コマンドをマッピングする
     *
     * @param string リクエストパターン
     * @param mixed クロージャか関数
     * @return Application
     */
    public function route($pattern, $action)
    {
        $this->router()->map($pattern, $action);
        return $this;
    }


    /**
     * アプリケーションを実行する
     */
    public function run ( )
    {
        // フラグ
        $executed = false;

        // コンポーネント
        $request  = $this->request();
        $response = $this->response();
        $router   = $this->router();

        // pre.runイベントを発生させる
        $this->trigger('pre.run', $request, $response, $router, $this);

        // リクエストがマッチするルートを取得
        while ( $route = $this->router()->route($request) ) {

            // 実行
            $this->trigger('pre.execute', $route, $request, $response, $this);
            $isContinue = $route->getCommand()->execute($request, $response, $this);
            $this->trigger('post.execute', $route, $request, $response, $this);

            // 実行の戻り値がtrueでなければ
            // これ以上マッチさせない
            if ($isContinue !== true) {
                $executed = true;
                break;
            }


            // ルータの内部ポインタを進める
            $this->router()->next();
        }

        // 実行したフラグがなければNOTFOUND
        if ($executed == false) {
            $this->debug($request->getUri()."はNOTFOUNDしました");
            $this->trigger('notfound', $request, $response, $this);
        }

        // post.runイベントを発生させる
        $this->trigger('post.run', $request, $response, $router, $this);
    }
}
