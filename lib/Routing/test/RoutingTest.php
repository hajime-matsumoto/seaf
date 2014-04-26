<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Routing;

use Seaf\Com;

/**
 * リクエストとルータをテストする
 */
class RoutingTest extends \PHPUnit_Framework_TestCase
{
    public function testInitRequest ( )
    {
        $Request = new Com\Request\Request( );
        $Request->init('/admin/index', ['a'=>'b']);
        $this->assertEquals (
            '/admin/index',
            $Request->getPath()
        );

        $Request->init('GET /home/index', ['a'=>'b']);
        $this->assertEquals (
            '/home/index',
            $Request->getPath()
        );

        $Request->mask('/home');
        $this->assertEquals (
            '/index',
            $Request->getPath()
        );

        $this->assertEquals (
            '/home/index',
            $Request->getPathWithoutMask()
        );

        $this->assertEquals (
            '/home',
            $Request->getMask()
        );
    }

    /**
     * インスタンス生成可能か
     */
    public function testSimpleGetRouting ( )
    {
        $Router  = new Router( );
        $Request = new Com\Request\Request( );
        $Router->map('GET /', function ( ) {
            return "aaaa";
        });
        $route = $Router->route($Request);
        $action = $route->getAction();

        $this->assertEquals(
            'aaaa',
            $action( )
        );
    }

    /**
     * インスタンス生成可能か
     */
    public function testMapWithParam ( )
    {
        $Router  = new Router( );
        $Request = new Com\Request\Request( );
        $Request->init('GET /name/hajime');
        $Router->map('GET /name/@name', function ($name) {
            return $name;
        });
        $route = $Router->route($Request);
        $closure = $route->getClosure( );
        $this->assertEquals(
            'hajime',
            $closure->invokeArgs($route->getParams())
        );
    }
}
