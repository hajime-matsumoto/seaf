<?php
namespace Seaf\View\Engine;

use Seaf\View;

/**
 * テンプレータ エンジン
 */
class Base
{
    protected $view;

    /**
     * __construct
     *
     * @param View $view
     */
    public function __construct (View\View $view)
    {
        $this->view = $view;
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
