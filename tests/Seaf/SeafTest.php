<?php
namespace Seaf\Tests;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Config\Config;
use Seaf\Core\Environment;
use Seaf\Loader\FileSystemLoader;

class SeafTest extends \PHPUnit_Framework_TestCase
{
	public function testGetInstance() 
	{
		$instance = Seaf::getInstance( );
		$this->assertInstanceOf('Seaf\Seaf', $instance);
	}

	/**
	 * @expectedException \Seaf\Loader\Exception\FileDoseNotExist
	 */
	public function testFileSystemLoaderNotExist( )
	{
		$loader = new FileSystemLoader(dirname(__FILE__));
		$loader->open('dose_not_exist.php');
	}

	public function testFileSystemLoaderExist( )
	{
		$loader = new FileSystemLoader(
			dirname(__FILE__).'/files'
		);
		$output =  $loader->read('dose_exist.php');
		$this->assertEquals('dose exist', trim($output));
	}

	public function testConfig( )
	{
		$loader = new FileSystemLoader(
			dirname(__FILE__).'/files'
		);
		$config = new Config( );
		$config->setFileLoader( $loader );
		$config->loadPHPFile('config.php');

		$config->setConfig('development.app.rec.rec', 'test');

		$this->assertArrayHasKey(
			'app',
			$config->getConfig('development')
		);

		$this->assertArrayHasKey(
			'env',
			$config->getConfig('development.app')
		);
	}

	public function testEnvironment( )
	{
		$root_path = dirname(__FILE__).'/files';
		$env = 'development';
		$environment = new Environment( );
		$environment->setEnvironmentName( $env );
		$environment
			->factory('get', 'fileLoader')
			->setParams(array($root_path)
		);
		$environment->component('get', 'config')
			->setFileLoader( $environment->component('get', 'fileLoader') )
			->loadPHPFile('config.php');
		$this->assertInstanceOf('Seaf\Core\Environment', $environment);
	}

	public function testBase( )
	{
		$root_path = dirname(__FILE__).'/files';
		$env = 'development';
		$base = new Base( );
		$base->init( $root_path, $env );

		$this->assertInstanceOf(
			'Seaf\Config\Config', $base->getConfig() 
		);
		$this->assertArrayHasKey(
			'env',
			$base->getConfig()->getConfig('development.app')
		);

		// test dispatch filter
		$base->action('test', function( $name = 'hajime'){
			return 'test:'.$name.':';
		});
		$base->before('test', function($params, &$out){
			$out.= 'before:test';
		});
		$base->after('test', function($params, &$out){
			$out.= 'after:test';
		});

		$out = $base->test( 'hajime' );
		$this->assertEquals('before:testtest:hajime:after:test', $out);
	}

	public function testExtends( )
	{
		$root_path = dirname(__FILE__).'/files';
		$env = 'development';
		Seaf::init( $root_path, $env );

		Seaf::action('route', function($pat, $func) use (&$routes){
			$routes[$pat] = $func;
		});

		Seaf::before('start', function($req, &$out){
			$out = '<h1>TITLE</h1>';
		});
		Seaf::action('start', function($url) use (&$routes) {
			return call_user_func($routes[$url]);
		});
		Seaf::after('start', function($req, &$out){
			$out.= '<footer>(c)2014</footer>';
			echo $out;
		});

		Seaf::route('/',function(){
			return "index";
		});

		$this->expectOutputString('<h1>TITLE</h1>index<footer>(c)2014</footer>');
		Seaf::start('/');
	}

	public function testNet( )
	{
		$root_path = dirname(__FILE__).'/files';
		$env = 'development';
		Seaf::init( $root_path, $env );

		Seaf::exten('web','Seaf\Net\WebExtension');
		Seaf::enable('web');

		Seaf::webMap('/', function(){
			echo 'hello web';
		});
		Seaf::webMap('/user/@id(/@name)', function($id, $name){
			echo 'hello '.$id.' '.$name;
		});

		// For Test Override Stop
		// Usual exit($body);
		Seaf::action('stop',function($body){
			echo $body;
		});

		Seaf::comp('webRequest')->url = '/user/100/hajime';

		$this->expectOutputString('hello 100 hajime');
		Seaf::webStart();
	}
}
