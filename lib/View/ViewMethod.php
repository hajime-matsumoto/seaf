<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View;

abstract class ViewMethod implements ViewMethodIF
{
    public function __construct (View\View $View)
    {
        $this->setupViewMethod($View);
    }

    /**
     *
     */
    public function render ($template, ViewModel $viewModel)
    {
        return $this->_render($template, $viewModel);
    }
}
