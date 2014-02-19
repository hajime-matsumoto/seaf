<?php
namespace Seaf\Tests;

use Seaf\Seaf;
use Monolog\Logger;

class SeafTest extends \PHPUnit_Framework_TestCase
{
	public function testGetInstance() {
		$instance = Seaf::getInstance( );
		$this->assertInstanceOf('Seaf\Seaf', $instance);
	}
}
