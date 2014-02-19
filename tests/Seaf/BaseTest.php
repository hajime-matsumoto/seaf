<?php
namespace Seaf\Tests;

use Seaf\Base;

class BaseTest extends \PHPUnit_Framework_TestCase
{
	public function testDispatch() 
	{
		$base = new Base();

		$base->before('helloWild',function(&$params, &$output){
			$output = '<h1>';
		});
		$base->after('helloWild',function(&$params, &$output){
			$output.= '</h1>';
		});

		$result = $base->helloWild('hajime');


		$this->assertEquals('<h1>hello wild hajime</h1>', $result);
	}
}
