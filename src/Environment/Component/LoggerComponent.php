<?php

namespace Seaf\Environment\Component;

use Seaf;
use Seaf\Log\Logger;
use Seaf\Environment\Environment;

class LoggerComponent extends Logger
{
    public function __construct() {
        parent::__construct();
        $this->writers =& Seaf::logger()->writers;
    }

    public function initComponent(Environment $env)
    {
        $this->name = $env->name;
    }
}
