<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Component;

use Seaf;
use Seaf\FW;
use Seaf\View\Base;
use Seaf\Data\Container;
use Seaf\Environment;
use Seaf\Web;
use Seaf\Kernel\Kernel;

/**
 * Viewコンポーネント
 */
class View extends Base
{
    const DEFAULT_TPL = 'index';

    /**
     * @var FW\Controller
     */
    private $controller;

    /**
     * Viewを有効化する
     *
     * @return $this
     */
    public function enable ( )
    {
        $this->controller->map('afterRun', array($this,'_afterRun'));
        $this->controller->map('beforeRun', array($this,'_beforeRun'));
        return $this;
    }

    /**
     * ControllerのBeforeRunをオーバライド
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
     * ControllerのAfterRunをオーバライド
     *
     * @param Request
     * @param Response
     * @param bool
     * @param Controller
     */
    public function _afterRun ($request, $response, &$dispatchFlag, $controller)
    {
        if ($dispatchFlag == false) {
            return false;
        }
        $params = $response->getParams();
        $params['contents'] = ob_get_clean();

        $tpl = $controller->get('template',self::DEFAULT_TPL);

        $response->clear()
            ->write($this->render($tpl, $params))
            ->send();
    }

    // --------------------------------
    // As Component
    // --------------------------------

    /**
     * Controllerを受け入れる
     *
     * @param FW\Controller
     */
    public function acceptController(FW\Controller $ctl)
    {
        $this->controller = $ctl;
        $this->addPath(Seaf::config('view.dirs'));
    }
}
