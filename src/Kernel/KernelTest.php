<?php
namespace Seaf\Kernel;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-11 at 19:39:51.
 */
class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Seaf\Kernel\Kernel::__callStatic
     */
    public function test__callStatic()
    {
        $this->assertInstanceOf(
            'Seaf\Kernel\Module\System',
            Kernel::system()
        );

        $this->assertInstanceOf(
            'ReflectionClass',
            Kernel::reflectionClass('Seaf\Kernel\Kernel')
        );

        $this->assertEquals(
            Kernel::system(),
            Kernel::system()
        );

        Kernel::system()->map('halt',function ( ) {
            return  'ok';
        });

        $this->assertEquals(
            'ok',
            Kernel::system()->halt()
        );
    }
}
