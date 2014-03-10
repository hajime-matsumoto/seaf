<?php
namespace Seaf\Web;

use Seaf\Core\Kernel;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-11 at 02:54:18.
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
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Viewをテストする
     */
    public function testView()
    {
        $app = $this->object;
        $result = "";

        Kernel::fs()->addFilePath(
            '/views', __DIR__.'/sample/views'
        );
        Kernel::system()->map('halt',function($body) use (&$result){
            $result = $body;
        });

        $app->view()->enable();
        $app->route('/',function($req, $res, $app){
            $app->set('template','index.twig');
            $res->param('title','Title');
            echo 'hi';
        });


        $app->run();

        ob_start();

        $this->assertEquals(
            "<h1>Title</h1><p>hi</p>",
            preg_replace('/[\r\n\t\s]/', '', $result)
        );
    }
}
