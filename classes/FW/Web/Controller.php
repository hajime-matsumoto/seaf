<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;

use Seaf\FW;

class Controller extends FW\Controller
{
    protected $name = 'Web';

    public function __construct ( ) 
    {
        $this->setComponentContainer('Seaf\FW\Web\ComponentContainer');

        $this->initWeb();
    }

    /**
     * 初期化する
     */
    public function initWeb ( )
    {
        $this->initController();

        // Mapを追加
        $this->bind($this, [
            'beforeRun' => '_beforeRun',
            'afterRun'  => '_afterRun',
            'notfound'  => '_notfound',
            'redirect'  => '_redirect'
        ]);

        // レスポンスを常に使用させる
        $this->on([
            'before.run' => 'beforeRun',
            'after.run'  => 'afterRun'
        ]);

    }

    /**
     * BeforeRun
     *
     * @param Event
     */
    public function _beforeRun ($event)
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
    public function _afterRun ($event)
    {
        if ($event->dispatchFlag == true) {
            $event->response
                ->write(ob_get_clean())
                ->send();
        }
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
        if ($uri{0} !== '/') {
            $uri = $this->request()->getPath($uri, false);
        }

        $this->response()
            ->clear()
            ->status(303)
            ->header('Location', $uri)
            ->write('<h1>Redirecting To '.$uri.'</h1>')
            ->send();
    }
}
