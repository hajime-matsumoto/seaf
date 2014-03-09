<?php

namespace Seaf\View\Engine;

use Seaf\View;

class Php
{
    private $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function render ($template, $params)
    {
        foreach($params as $k =>$v) {
            $$k = $v;
        }

        ob_start();
        include $this->getFile($template);
        return ob_get_clean();
    }

    public function getFile($template)
    {
        foreach ($this->view->getPaths() as $path) {
            $file = $path.'/'.$template.'.php';
            if (file_exists($file)) {
                return $file;
            }
        }
    }
}
