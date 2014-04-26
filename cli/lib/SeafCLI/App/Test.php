<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace SeafCLI\App;

use Seaf\CLI;

class Test extends CLI\CLIController
{
    public function setupController ( )
    {
        parent::setupController();
        $this->setupByAnnotation();
    }

    /**
     * @SeafRoute /
     * @SeafRoute /index
     */
    public function index ( )
    {
        $this->stdout('こん');
        $this->info('あああ');
    }
}
