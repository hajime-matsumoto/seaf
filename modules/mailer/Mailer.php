<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Mailer;

use Seaf;
use Seaf\Pattern;
use Seaf\View;

class Mailer
{
    use Pattern\Configure;

    private $view;

    public static function factory ($config) 
    {
        $mailer = new static( );
        $mailer->configure($config);
        return $mailer;
    }

    public function view ( )
    {
        return $this->view;
    }

    public function configView ($config)
    {
        $this->view = new View\Base();
        $this->view->addPath($config['path']);
    }

    public function mail( )
    {
        return new Mail($this);
    }
}
