<?php
namespace Seaf\Core;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-10 at 18:10:09.
 */
class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Kernel
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        Kernel::init();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Seaf\Kernel\Kernel::singleton
     */
    public function testSingleton()
    {
        $k = Kernel::singleton();
        $this->assertEquals($k, Kernel::singleton());
    }

    /**
     * @covers Seaf\Kernel\Kernel::init
     */
    public function testInit()
    {
        Kernel::init(array(
            'vars'=>array(
                'SERVER'=>array(
                    'REQUEST_URI' => '/'
                )
            ),
            'fileSystem'=>array(
                '/'=>__DIR__
            ),
            'classLoader'=>array(
                'namespaces'=>array(
                    'Seaf'=>'/tmp/Seaf'
                )
            )
        ));

        $this->assertEquals(
            __FILE__,
            Kernel::fileSystem()->transRealPath(basename(__FILE__))
        );
    }
}
