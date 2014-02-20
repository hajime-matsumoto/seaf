<?php
namespace Seaf\Tests;

use Seaf\UI\Container as UIContainer;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
	public function testConteiner()
	{
		$container = new UIContainer();
		$this->assertInstanceOf('Seaf\UI\Container', $container);

		# try it simple
		$container->addFactory('dispatcher', 'Seaf\Dispatcher');
		$f = $container->getFactory('dispatcher');
		$this->assertInstanceOf('Closure', $f);
		$dispatcher = $container->newInstance('dispatcher');
		$this->assertInstanceOf('Seaf\Dispatcher', $dispatcher);
	}

	public function testConteinerCallback()
	{
		$container = new UIContainer();
		$this->assertInstanceOf('Seaf\UI\Container', $container);

		$container->addFactory(
			'dispatcher', 
			'Seaf\Dispatcher', 
			array(), 
			function($dispatcher){
				$dispatcher->setMethod('test', function(){
					return 'test';
				});
				return $dispatcher;
			}
		);

		$dispatcher = $container->newInstance('dispatcher');
		$this->assertInstanceOf('Seaf\Dispatcher', $dispatcher);
		$result = $dispatcher->run('test');
		$this->assertEquals('test', $result);


		$dispatcher2 = $container->getInstance('dispatcher');
		$dispatcher2->setMethod('test2', function(){
			return 'test2';
		});
		$result = $dispatcher2->run('test2');
		$this->assertEquals('test2', $result);

		$dispatcher3 = $container->getInstance('dispatcher');

		$result = $dispatcher3->run('test2');
		$this->assertEquals('test2', $result);
	}
}
