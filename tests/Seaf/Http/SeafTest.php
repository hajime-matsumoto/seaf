<?php
namespace Seaf\Web\Tests;

use Seaf\Seaf;

class SeafTest extends \PHPUnit_Framework_TestCase
{
	public function testSimpeWeb(){
		Seaf::extension();

		Seaf::enable('http');

		Seaf::before('start', function( ){
			echo '<html>';
			echo ' <body>';
		});
		Seaf::after('start', function( ){
			echo ' </body>';
			echo '</html>';
		});
		Seaf::route('/', function( ){
			echo '<h1>Hello World!</h1>';
		});

		Seaf::start();
	}
}
