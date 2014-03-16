<?php
namespace Seaf\Application\Component;

use Seaf\Environment\Environment;
use Seaf\Environment\Component\ComponentIF;
use Seaf\Net\Session\Base;

/**
 * Session
 */
class Session extends Base implements ComponentIF
{
    private $env;

    public function initComponent(Environment $env)
    {
        $this->env = $env;
    }
}

