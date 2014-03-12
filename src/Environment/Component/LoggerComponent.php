<?php

namespace Seaf\Environment\Component;

use Seaf;
use Seaf\Log\Logger;
use Seaf\Log\Level;
use Seaf\Environment\Environment;

class LoggerComponent extends Logger
{
    private $env;

    public function __construct() {
        parent::__construct();
        $this->writers =& Seaf::logger()->writers;
    }

    public function initComponent(Environment $env)
    {
        $this->name = $env->name;
        $this->env = $env;
    }

    public function importHelper(Environment $env)
    {
        foreach (Level::$map as $k=>$v)
        {
            $this->env->map(strtolower($v),array($this,strtolower($v)));
        }
    }
}
