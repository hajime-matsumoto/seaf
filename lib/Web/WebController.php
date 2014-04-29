<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\Controller;
use Seaf\Component;
use Seaf\Wrapper;
use Seaf\Event;
use Seaf\Session;
use Seaf\Com\Result;

/**
 * コントローラ
 */
class WebController extends Controller\Controller
{
    /**
     * コントローラをイニシャライズする
     */
    protected function setupController ( )
    {
        parent::setupController ( );
        $this->setupWebController( );
    }

    /**
     * Webコントローラをセットアップする
     */
    protected function setupWebController ( )
    {
    }

    /**
     * コンポーネントローダをセットアップする
     */
    protected function setupComponentLoader( )
    {
        parent::setupComponentLoader ( );

        $this->addComponentLoader(
            new Component\Loader\NamespaceLoader(
                __NAMESPACE__.'\\Component'
            )
        );

        // コンポーネント作成時の処理を追加
        $this->on('component.create', function ($e) {
            $instance = $e->getVar('component');
            if ($instance instanceof \Seaf\Web\Component\ComponentIF) {
                $instance->setupWebComponent($this);
            }
        });
    }

    /**
     * NotFoundさせる
     */
    public function notfound ($msg = '')
    {
        $this->Result( )
            ->clear()
            ->status(Result\StatusCode::NOT_FOUND)
            ->write(
                '<h1>'.
                'Page Not Found'.
                '<small style="margin-top: 10px;display:block">'.
                'Url: ' . ($this->Request()->getPathWithoutMask()).
                '</small>'.
                '</h1>'.
                $msg
            )->send( );
    }

    /**
     * セッションを取得する
     */
    public function initSession ($cfg)
    {
        $Session = Session\Session::getSingleton( );
        $Session->setup($cfg);
        return $Session;
    }

    /**
     * Web用のリクエストをセットアップする
     *
     * @return Request
     */
    public function initRequest( )
    {
        $Request = parent::initRequest( );
        $g = Wrapper\SuperGlobalVars::getSingleton();

        // メソッドを取得
        $Request->method(
            $g('_SERVER.REQUEST_METHOD', 'GET')
        );

        // パスを取得
        $Request->path($g('_SERVER.REQUEST_URI', '/'));

        // パラメタ
        $Request->param($g('_REQUEST',[]));

        if ($Request->getMethod() == 'PUT') {
            $Request->importQueryString(
                file_get_contents('php://input')
            );
        }
        return $Request;
    }

    /**
     * Web用のリザルトはネームスペースを使う
     *
     * @return Result
     */
    public function initResult ( )
    {
        return false;
    }

}
