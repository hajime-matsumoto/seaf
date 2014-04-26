<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\CLI;

/**
 * AdminController
 */
class TestAdminController extends CLIController
{
    public function setupController ( ) 
    {
        parent::setupController();

        $this->setupByAnnotation($this);
    }

    /**
     * @var test
     */
    private $test;

    /**
     * トップページ
     *
     * @SeafRoute /
     */
    public function index ($Request, $Result, $Ctrl)
    {
        $Result->write('AAA');
    }

    /**
     * @SeafRoute /index2
     */
    public function index2 ($Request, $Result, $Ctrl)
    {
        $Result->write('BBB');
    }

    /**
     * @SeafEventOn notfound
     */
    public function notfound ( )
    {
        $Result->write('NOT FOUND');
    }
}

/**
 * CLIコントローラ
 */
class CLIControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCLIControllerInstance( )
    {
        $Controller = new CLIController( );
        $Controller->route('/', function ($Request, $Result, $Ctrl) {
            $Result->write('test');
        });
        $Controller->mount('/admin', 'Seaf\CLI\TestAdminController');
        $Controller->Request()->init('/admin');
        $Controller->run();

        $this->assertEquals(
            'AAA',
            $Controller->Result( )->getBody()
        );

        $Controller->Result( )->clear( );
        $Controller->Request()->init('/admin/index2');
        $Controller->run();

        $this->assertEquals(
            'BBB',
            $Controller->Result( )->getBody()
        );

        $Controller->Result( )->clear( );
        $Controller->Request()->init('/');
        $Controller->run();

        $this->assertEquals(
            'test',
            $Controller->Result( )->getBody()
        );
    }
}
