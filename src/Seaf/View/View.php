<?php

namespace Seaf\View;

use Seaf\Environment\Environment;
use Seaf\Commander\Command;

class View extends Environment
{
    const DEFAULT_ENGINE = 'php';

    private $params = array();

    private $paths;
    private $layout = false;

    public function __construct( )
    {
        parent::__construct();

        $this->set('engine', self::DEFAULT_ENGINE);
    }

    public function param($k,$v)
    {
        $this->params[$k] = $v;
        return $this;
    }

    public function addPath ($path)
    {
        $this->paths[] = realpath($path);
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getEngine($name)
    {
        $engine = Command::newInstanceArgs(
            __NAMESPACE__.'\\Engine\\'.ucfirst($name),
            array($this)
        );
        return $engine;
    }

    public function render ($template, $params)
    {
        $params += $this->params;


        return $this->getEngine($this->get('engine'))->render($template, $params);
    }
}