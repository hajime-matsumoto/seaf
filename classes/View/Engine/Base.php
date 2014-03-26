<?php
namespace Seaf\View\Engine;

use Seaf\View;

/**
 * テンプレータ
 */
class Base extends View\Base
{
    /**
     * __construct
     *
     * @param View $view
     */
    public function __construct (View\Base $view)
    {
        parent::__construct();
        $this->addPath($view->paths);
    }

    /**
     * 描画
     *
     * @param string $file
     * @param array $vars
     */
    public function render($file, $vars = array())
    {
    }
}
