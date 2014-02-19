<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Extension\HTTP;

use ArrayObject;

class Request extends ArrayObject 
{

	public  function __construct($data = array())
	{
		parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
		$this->POST = $_POST;
		$this->COOKIE = $_POST;
		$this->FILES = $_FILES;
		$this->GET = $_GET;
		$this->SERVER = $_SERVER;
	}

	public function __get($key) 
	{
		switch($key){
		case 'url':
			return $thi->url();
		case 'method':
			return $this->method();
		default:
			return parent::__get($key);
		}
	}

	public function url()
	{
		return  $this->SERVER['REQUEST_URI'];
	}

	public function method() 
	{
		return $this->getMethod();
	}

	private function getMethod()
	{
		if ( isset($this->SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ) {
			return $this->SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}elseif (isset($this->REQUEST['_method'])) {
			return $this->REQUEST['method'];
		}
		return $this->getEnv('REQUEST_METHOD', 'GET');
	}

	private function getEnv($var, $default = '') 
	{
		return isset($this->SERVER[$var]) ? $this->SERVER[$var]: $default;
	}

}
