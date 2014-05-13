<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\BackEnd;

/**
 * レジストリモジュールのテスト
 */
class RegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testRegistry ( )
    {
        $m = Manager::getSingleton();


        // Registryモジュール
        $registry = $m->registry;

        $registry->debugOn();
        $this->assertTrue($registry->isDebug());
        $registry->debugOff();
        $this->assertFalse($registry->isDebug());
        $registry->set('dir', __DIR__);
        $this->assertEquals(__DIR__, $registry->get('dir'));
        $this->assertTrue($registry->has('dir'));

        $registry->phpRegister();
    }
}
