<?php
namespace Seaf\Application;

use Seaf\Environment\Environment as EnvironmentBase;
/**
 * アプリケーション用のEnvironment
 *
 */
class Environment extends EnvironmentBase
{
    public $application;

    public function __construct(Base $application)
    {
        parent::__construct( );
        $this->di()->addComponentNamespace(__CLASS__);
        $this->application = $application;
    }
}
