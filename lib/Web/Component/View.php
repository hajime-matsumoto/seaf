<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;

use Seaf\Web;

use Seaf\View\View as Base;

/**
 * コントローラ
 */
class View extends Base implements ComponentIF
{
    /**
     * @var Web\WebController
     */
    private $Controller;

    /**
     * WebControllerをセットアップする
     */
    public function setupWebComponent(Web\WebController $Ctrl)
    {
        $this->Controller = $Ctrl;
    }

    /**
     * Viewを有効にする
     */
    public function enable ()
    {
        $this->Controller->setMethod([
            'display' => [$this, '_display'],
            'render' => [$this, '_render']
        ]);
    }

    /**
     * ディスプレイ
     */
    public function _display ($template, $vars = [])
    {
        return parent::display($template, $vars);
    }

    /**
     * レンダー
     */
    public function _render ($template, $vars = [])
    {
        return parent::render($template, $vars);
    }
}
