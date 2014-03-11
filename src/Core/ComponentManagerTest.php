<?php
namespace Seaf\Core;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-10 at 21:03:36.
 */
class ComponentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ComponentManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $env = new Environment();
        $this->object = new ComponentManager($env);

        // グローバルを作っておく
        $this->global = ComponentManager::getGlobal();
        $this->global->register('factory', 'Seaf\Core\DI\Factory');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Seaf\Core\ComponentManager::addNamespace
     */
    public function testAddNamespace()
    {
        $this->assertInstanceOf(
            'Seaf\Core\Component\EchoComponent',
            $this->object->get('echo')
        );
    }

    /**
     * グローバルを見にゆくか？
     */
    public function testGlobalComponentManager()
    {
        $this->assertTrue($this->object->has('factory'));
        $this->assertInstanceOf(
            'Seaf\Core\DI\Factory',
            $this->object->get('factory')
        );
    }
}
