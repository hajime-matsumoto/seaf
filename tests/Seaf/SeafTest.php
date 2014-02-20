<?php
namespace Seaf\Tests;

use Seaf\Seaf;

class SeafTest extends \PHPUnit_Framework_TestCase
{
	public function testGetInstance() {
		$instance = Seaf::getInstance( );
		$this->assertInstanceOf('Seaf\Seaf', $instance);
	}

	public function testExtension() {
		$ext = Seaf::extension();
		$this->assertInstanceOf('Seaf\Extension\ExtensionManager', $ext);

		Seaf::exten('test', 'Seaf\Extension\TestExtension');
		Seaf::enable('test');

		Seaf::echoHelloWorld();
	}
}
