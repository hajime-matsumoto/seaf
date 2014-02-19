<?php
namespace Seaf\Tests;

use Seaf\Dispatcher;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
	public function testDispatch() 
	{
		$dispatcher = new Dispatcher();
		$dispatcher->setMethod('test', function($a, $b, $c){
			return "abc".($a+$b+$c);
		});

		$result = $dispatcher->run('test', array(1,2,3));
		$this->assertEquals('abc6', $result);
	}

	public function testDispatchHook() 
	{
		$dispatcher = new Dispatcher();

		$dispatcher->setMethod('test', function($name){return $name;});
		$dispatcher->hook('test','before',function(&$params, &$output){
			$output = '<h1>';
		});
		$dispatcher->hook('test','after',function(&$params, &$output){
			$output.= '</h1>';
		});

		$result = $dispatcher->run('test', array('hajime'));

		$this->assertEquals('<h1>hajime</h1>', $result);
	}
}
