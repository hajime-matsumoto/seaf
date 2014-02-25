<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Web Application
 */
require_once '/path/to/vendor/autoload.php';

use Seaf\Net\WebApp;

class App extends WebApp 
{
    /**
     * @route *
     * @method POST|GET|PUT
     */
    public function SayHello( )
    {
        echo 'Hello Wild';
    }
}
