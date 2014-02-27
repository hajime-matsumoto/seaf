<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class WebTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        Seaf::useExtension('web');
    }

    public function testWebStart()
    {
        $router = Seaf::retrieve('webRouter');
        $request = Seaf::retrieve('webRequest');

        Seaf::remap('stop', function($body){
            echo $body;
        });

        $router->map('/', function(){
            return 'index';
        });

        $request->url = '/';

        $route = $router->route( $request );

        $this->assertEquals( 'index', call_user_func($route->callback));


        $request->url = '/user';

        $buf ="";
        Seaf::on('webStop.before', function() use (&$buf){
            $buf = ob_get_clean();
        });

        Seaf::on('webStart.before', function(){
            echo 'aaaa';
        });
        Seaf::on('webStart.after', function(){
            echo 'bbbb';
        });

        Seaf::webRoute('/user', function(){
            echo 'user';
        });

        Seaf::webStart( );

        $this->assertEquals('aaaauserbbbb', $buf);

        ob_start();
    }
}
