<?php
namespace Seaf\Test;

use Seaf\Seaf;
use Seaf\Kernel\Kernel;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-12 at 22:59:30.
 */
class SeafConsoleTest extends \PHPUnit_Framework_TestCase
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
        Kernel::Globals( )->set('argv', array(
            'file',
            'init'
        ));
        Kernel::System( )->map('halt', function ($body) {
        });
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Console Application
     */
    public function testConsoleApplication ( )
    {
        $this->assertInstanceOf(
            'Seaf\Application\Console\Base',
            Seaf::Console()
        );

        $this->assertInstanceOf(
            'Seaf\Application\Console\Component\Request',
            Seaf::Console()->request()
        );


        Seaf::Console()->route('init', function ($req, $res) {
            echo 'いにしゃらいず';
        })->route('install', function ($req, $res) {
            echo 'いんすとーる';
        })->run( );

        $this->assertEquals(
            'init',
            Seaf::Console()->request()->uri
        );

        $this->assertEquals(
            'いにしゃらいず',
            Seaf::Console()->response()->body
        );

        Seaf::Console()->request()->setUri('install');
        Seaf::Console()->response()->init();
        Seaf::Console()->run();
        $this->assertEquals(
            'いんすとーる',
            Seaf::Console()->response()->body
        );

    }

}
