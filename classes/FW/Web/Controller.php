<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;

use Seaf\FW;

class Controller extends FW\Controller
{
    protected $name = 'Web';

    public function __construct ( ) 
    {
        $this->initWeb();
    }

    /**
     * 初期化する
     */
    public function initWeb ( )
    {
        $this->initController();

        // コンポーネントネームスペースを追加
        $this->di( )->factory->configAutoLoad(__NAMESPACE__.'\\Component\\');

        // Mapを追加
        $this->bind($this, [
            'beforeRun' => '_beforeRun',
            'afterRun'  => '_afterRun',
            'notfound'  => '_notfound',
            'redirect'  => '_redirect'
        ]);

        // レスポンスを常に使用させる
        $this
            ->on('before.run', 'beforeRun')
            ->on('after.run', 'afterRun');

    }

    /**
     * BeforeRun
     *
     * @param Request
     * @param Response
     * @param Controller
     */
    public function _beforeRun ($request, $response, $controller)
    {
        ob_start();
    }

    /**
     * AfterRun
     *
     * @param Request
     * @param Response
     * @param bool
     * @param Controller
     */
    public function _afterRun ($request, $response, $dispatchFlag, $controller)
    {
        if ($dispatchFlag == false) {
            $controller->notfound($request, $response, $controller);
        }
        $response
            ->write(ob_get_clean())
            ->send();
    }

    /**
     * NotFound
     *
     * @param Request
     * @param Response
     * @param Controller
     */
    public function _notfound ($request, $response, $controller)
    {
        $response
            ->status(404)
            ->write('<h1>404 Not Found</h1>')
            ->send();
    }

    /**
     * Redirect
     *
     * @param Request
     * @param Response
     * @param Controller
     */
    public function _redirect ($uri)
    {
        // パスを変換する
        $uri = $this->request()->uri()->abs($uri);
        $this->response()
            ->clear()
            ->header('Location', $uri)
            ->status(303)
            ->write('<h1>Redirecting To '.$uri.'</h1>')
            ->send();
    }
}
