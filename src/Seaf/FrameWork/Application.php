<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork;

use Seaf\Environment\Environment;
use Seaf\Commander\Command;

/**
 * Cli Application
 */
class Application extends Environment
{
    public function __construct ( )
    {
        parent::__construct();

        $this->initApplication();
    }

    public function init ( ) {
        parent::init();
    }

    public function route($pattern, $action)
    {
        $this->router()->map($pattern, $action);
        return $this;
    }

    public function initApplication( ) 
    {
    }

    public static function singleton ( )
    {
        return new self();
    }

    public function run ( )
    {
        $executed = false;


        $this->trigger('pre.run');
        if ($request == null) {
            $request = $this->request();
        }

        while ( $route = $this->router()->route($request) )
        {
            $this->debug($route->getCommand().'を実行します');

            $isContinue = $this->execute($route->getCommand());

            if ($isContinue == false) {
                $executed = true;
                break;
            }


            $this->router()->next();
        }
        $this->trigger('post.run');

        if ($executed == false) {
            $this->trigger('notfound');
            $this->notfound();
        }
    }

    public function execute(Command $command)
    {
        return $command->execute();
    }

    public function notfound( )
    {
        echo 'Not Found';
    }

}
