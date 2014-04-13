<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core;

class SeafTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleton ( )
    {
        $s = Seaf::singleton();
        $this->assertInstanceOf('Seaf\Core\Seaf', $s);

        $this->assertEquals(
            $s,
            Seaf::singleton()
        );
    }

    public function testNamespaceBaseFactory ( )
    {
        $system = Seaf::System();
        $this->assertInstanceOf('Seaf\Core\Component\System', $system);
    }

    public function testMethodBaseFactory ( )
    {
        $reg = Seaf::Reg();
        $this->assertEquals($reg('root'), SEAF_PROJECT_ROOT);
    }

    public function testConfig ( )
    {
        $cfg = Seaf::Config();
        $this->assertEquals(
            'utf-8', $cfg('encoding')
        );
    }

    public function testStorage ( )
    {
        $this->assertEquals(
            'Memcache',
            Seaf::Cache()->storage()->getType()
        );
    }
}
