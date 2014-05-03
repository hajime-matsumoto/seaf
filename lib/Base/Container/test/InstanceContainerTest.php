<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Container;

use Seaf\Util\Util;
use Seaf\Base\Event;

class InstanceContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanceContainer ( )
    {
        $c = new InstanceContainer( );

        $this->assertInstanceOf(
            'Seaf\Base\Container\InstanceContainer',
            $c
        );
    }

    public function testRegisterClass ( )
    {
        $c = new InstanceContainer();
        $c->getFactory()->register(
            $class = 'Seaf\Base\Container\ArrayContainer', 
            $args = [['hogehoge' => 'hugahuga']],
            $options = ['alias'=>'container']
        );
        $this->assertTrue($c->getFactory()->canCreate('container'));
    }

    public function testCleateInstance ( )
    {
        $c = new InstanceContainer();
        $c->getFactory()->register(
            $class = 'Seaf\Base\Container\ArrayContainer', 
            $args = [['hogehoge' => 'hugahuga']],
            $options = ['alias'=>'container']
        );
        $this->assertEquals(
            'hugahuga',
            $c->newInstance('container')->hogehoge
        );
    }

    public function testGetInstance( )
    {
        $c = new InstanceContainer();
        $c->getFactory()->register(
            $class = 'Seaf\Base\Container\ArrayContainer', 
            $args = [['hogehoge' => 'hugahuga']],
            $options = ['alias'=>'container']
        );

        $c->getInstance('container')->hoge = 'huga';
        $c->getInstance('container')->hogella = 'huga';
        $this->assertEquals(
            'huga',
            $c->getInstance('container')->hoge
        );
    }

    public function testEventHandling( )
    {
        $c = new InstanceContainer();
        $c->getFactory()->register(
            $class = 'Seaf\Base\Container\ArrayContainer', 
            $args = [['hogehoge' => 'hugahuga']],
            $options = ['alias'=>'container']
        );

        $c->addObserver(new Event\ObserverCallback(function($e) use(&$cnt) {
            $key = get_class($e->target).' '.$e->type;
            if(!isset($cnt[$key])) $cnt[$key] = 1;
            $cnt[$key]++;
        }));

        $c->getInstance('container')->hoge = 'huga';
        $c->getInstance('container')->hogella = 'huga';
        $this->assertEquals(
            'huga',
            $c->getInstance('container')->hoge
        );
        $this->assertEquals(
            2,
            $cnt['Seaf\Base\Container\InstanceContainer before.newInstanceArgs']
        );

        $c->addObserver(new Event\ObserverCallback(function($e) use(&$cnt) {
            $key = get_class($e->target).' '.$e->type;

            if ($key == 'Seaf\Base\Container\InstanceContainer before.newInstanceArgs') {
                if ($e->name == 'container')
                {
                    if(empty($e->args)) {
                        $e->args = [['hehehe'=>'hohoho']];
                    }
                }
            }
        }));


        $this->assertEquals(
            'hohoho',
            $c->newInstance('container')->hehehe
        );
    }
}
