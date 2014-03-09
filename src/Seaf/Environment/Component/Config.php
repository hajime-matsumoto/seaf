<?php
namespace Seaf\Environment\Component;

use Seaf\Config\Config as SeafConfig;
use Seaf\Environment\Environment;

class Config extends SeafConfig
{
    public function __construct (Environment $env)
    {
        $this->env = $env;
        parent::__construct($env->get('ENV','development'));
    }
}
