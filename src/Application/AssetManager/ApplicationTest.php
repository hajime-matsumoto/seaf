<?php
namespace Seaf\Application\AssetManager;
use Seaf\Kernel\Kernel;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-12 at 22:59:30.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Application;

        Kernel::system()->map('halt',function(){});
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Seaf\Application\AssetManager\Application::initApplication
     * @todo   Implement testInitApplication().
     */
    public function _testInitApplication()
    {
        $this->object->request('GET echo?name=hajime');
        $this->object->router()->map(
            'echo', function ($req, $res) {
                echo 'echo';
            }
        )->map(
            'echo2', function ($req, $res) {
                echo $req['name'];
            }
        );

        $this->object->run();
        $this->assertEquals(
            'echo',
            $this->object->response()->body
        );

        $this->object->request('GET echo2?name=hajime');
        $this->object->response()->init();
        $this->object->run();
        $this->assertEquals(
            'hajime',
            $this->object->response()->body
        );
    }

    public function testSass()
    {
        $this->object->request('GET /styles/app.css');
        ob_start();
        $this->object->run();
        $css = ob_get_clean();
        $this->assertTrue(strlen($css)>2000);

    }

    public function testCoffee()
    {
        $this->object->request('GET /scripts/app.js');
        ob_start();
        $this->object->run();
        $js = ob_get_clean();
        $this->assertTrue(strlen($js)>900);
    }

    public function testCoffeeSafe()
    {
        $this->object->request('GET /scripts/app.js\'\"`stat`');
        $this->object->run();
    }
}