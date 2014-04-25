<?php // vim: set ft=php ts=4 sts=4 sw=4 et:


class SeafTest extends \PHPUnit_Framework_TestCase
{
    /**
     * インスタンス生成可能か
     */
    public function testInitialize ( )
    {
        $s = Seaf::getSingleton( );

        $this->assertInstanceOf(
            'Seaf',
            $s
        );
    }

    public function testRegistryOK ( )
    {
        $s = Seaf::getSingleton( );
        $r = $s->getComponent('Registry');
        $this->assertEquals(
            $r,
            Seaf::Registry()
        );
    }
}
