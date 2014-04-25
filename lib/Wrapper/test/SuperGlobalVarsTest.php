<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Wrapper;

class SuperGlobalVarsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * インスタンス生成可能か
     */
    public function testInitialize ( )
    {
        $g = new SuperGlobalVars( );

        $this->assertInstanceOf(
            'Seaf\Wrapper\SuperGlobalVars',
            $g
        );
    }

    /**
     * 正常に値が取得できるか?
     */
    public function testGetter ( )
    {
        $g = new SuperGlobalVars( );

        $this->assertTrue(is_array($g('_SERVER')));
    }

    /**
     * 正常に値が設定できるか?
     */
    public function testSetter ( )
    {
        $g = new SuperGlobalVars( );

        $g->setVar('_SERVER', [
            'NAME' => 'hazime.org',
            'IP' => '127.0.0.1'
        ]);

        $this->assertEquals(
            'hazime.org',
            $g('_SERVER.NAME')
        );
        $g->setVar('_SERVER.NAME', 'www.hazime.org');

        $this->assertEquals(
            'www.hazime.org',
            $g('_SERVER.NAME')
        );
    }

}
