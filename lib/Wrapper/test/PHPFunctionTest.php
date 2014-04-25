<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

class PHPFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * インスタンス生成可能か
     */
    public function testInitialize ( )
    {
        $p = new PHPFunction( );

        $this->assertInstanceOf(
            'Seaf\Wrapper\PHPFunction',
            $p
        );
    }

    /**
     * オーバライドされていない関数を呼び出す
     */
    public function testNoneOrverRide ( )
    {
        $p = new PHPFunction( );

        $this->assertEquals(
            'Abc',
            $p('ucfirst', 'abc')
        );
    }

    /**
     * オーバライドする
     */
    public function testOrverRide ( )
    {
        $p = new PHPFunction( );

        $p->setMethod('ucfirst', function ($str) {
            return strtoupper($str);
        });

        $this->assertEquals(
            'ABC',
            $p('ucfirst', 'abc')
        );
    }
}
