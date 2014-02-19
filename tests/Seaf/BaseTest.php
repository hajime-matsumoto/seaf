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

	public function testExtension()
	{
		$base = new Base();
		$em = $base->extension();
		$this->assertInstanceOf('Seaf\Extension\ExtensionManager', $em);

		// Register Test EXT
		$base->extension()->register('test', 'Seaf\Extension\TestExtension');
		// Enable Test Extension
		$base->extension()->enable('test');

		$base->init();
		$base->extension(); // Extensionを有効にする
		$base->exten('test', 'Seaf\Extension\TestExtension');
		$base->enable('test'); // Test Extensionを有効にする


		$base->echoHelloWorld();
		$this->assertEquals("Hello World", $base->retHelloWorld());

		// filter test
		$base->after('retHelloWorld', function($params, &$output) {
			$output.='!!';
		});

		$this->assertEquals("Hello World!!", $base->retHelloWorld());
	}
}
