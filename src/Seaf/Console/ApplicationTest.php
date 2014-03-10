<?php
namespace Seaf\Console;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-09 at 16:00:52.
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
     * @covers Seaf\Console\Application::initApplication
     */
    public function testInitApplication()
    {
        $this->object->initApplication();
    }

    public function testApplication( )
    {
        $app = $this->object;

        $app->route('init', function ( ) use ($app) {
            $app->out ('初期化します');
            $answer = $app->in ('よろしいですか？ y or n ', 'y');

            if ($answer === 'n') {
                $app->redirect('end');
            }
        });

        $app->system()->in();

        $app->route('end', function ( ) use ($app) {
            $app->out ('終了します');
        });

        $app->run('init');
    }
}