<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Factory;

use Seaf\Util\Util;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testRegister ( )
    {
        $factory = new Factory( );

        $factory->register('Seaf\Base\Factory\Factory');

        $instance = $factory->newInstance('Seaf\Base\Factory\Factory');
    }

    /**
     *  チェインオブレスポンシビリティのテスト
     */
    public function testCoR ( )
    {
        $factory = new Factory( );

        $factoryA = new Factory( );
        $factoryA->register('Seaf\Base\Factory\Factory');

        $factoryB = new Factory( );
        $factoryB->register('Seaf\Base\Container\ArrayContainer');

        $this->assertFalse($factory->newInstance('Seaf\Base\Factory\Factory'));
        $this->assertFalse($factory->newInstance('Seaf\Base\Container\ArrayContainer'));

        $factory->setNext($factoryA, $factoryB);
        $this->assertInstanceOf(
            'Seaf\Base\Factory\Factory',
            $factory->newInstance('Seaf\Base\Factory\Factory')
        );
        $this->assertInstanceOf(
            'Seaf\Base\Container\ArrayContainer',
            $factory->newInstance('Seaf\Base\Container\ArrayContainer')
        );
    }
}
