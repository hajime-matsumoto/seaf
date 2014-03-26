<?php
namespace Seaf\Core\Component;

use Seaf\Pattern;
use Seaf\DI;

class Helper
{
    use Pattern\DynamicMethod;

    private $di;

    public function __construct ( )
    {
        $this->di = new DI\Container( );

        $this->bind($this->di, array(
            'register' => 'register',
        ))->bind($this, array(
            'get' => '_get'
        ));
    }

    public function callFallBack($name, $params)
    {
        return $this->di->call($name);
    }

    public function getKeys( ) 
    {
        $keys = $this->di->getKeys();
        return $keys;
    }

    public function _get ($name)
    {
        return $this->di->get($name);
    }

    public function __invoke ($name)
    {
        return $this->get($name);
    }
}
