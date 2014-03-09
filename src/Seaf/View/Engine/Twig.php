<?php

namespace Seaf\View\Engine;

use Seaf\View\View;

if (!class_exists('\Twig_Loader_Filesystem')) {
    require_once 'Twig/Autoloader.php';
    \Twig_Autoloader::register();
}

class Twig
{
    private $view;
    private $twig;

    public function __construct(View $view)
    {
        $this->view = $view;

        $loader = new \Twig_Loader_Filesystem($view->getPaths());
        $this->twig   = new \Twig_Environment(
            $loader, 
            array(
                'cache' => realpath('/tmp/twig')
            )
        );

        $this->twig->clearcachefiles();
    }

    public function render ($template, $params)
    {
        return $this->twig->render($template.".twig", $params);
    }
}
