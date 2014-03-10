<?php
namespace Seaf\App;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-11 at 00:35:25.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Application;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Seaf\App\App::initApp
     */
    public function testInitApp()
    {
        $this->assertInstanceOf(
            'Seaf\App\Component\EventComponent', 
            $this->object->event()
        );
    }

    public function testEvent()
    {
        $result = false;
        $this->object->on('event', $func = function()use(&$result){
            $result = true;
        });

        $this->assertFalse($result);

        $this->object->trigger('event');
        $this->object->off('event', $func);

        $this->assertTrue($result);

        $result = false;
        $this->assertFalse($result);
        $this->object->trigger('event');
        $this->assertFalse($result);
    }


    /**
     * @covers Seaf\App\App::_route
     * @covers Seaf\App\App::_run
     * @covers Seaf\App\App::_execute
     * @todo   Implement test_route().
     */
    public function test_route()
    {
        $this->object->route('/', function($req, $res, $app) {
            echo 'Hello World';
        });
        $this->object->route('/name/@name', function($name, $req, $res, $app) {
            echo 'Hello '.$name;
        });

        ob_start();
        $this->object->run();
        $this->object->request()->init(array(
            'uri'    => '/name/hajime',
            'method' => 'GET'
        ));
        $this->object->run();
        $result = ob_get_clean();

        $this->assertEquals(
            'Hello WorldHello hajime',
            $result
        );
    }
}
