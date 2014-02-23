<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Net;

use ArrayObject;

/**
 * Request Controll Object
 */
class Request extends ArrayObject 
{

	public  function __construct($data = array())
	{
		parent::__construct($data);
		$this->POST   = $_POST;
		$this->COOKIE = $_POST;
		$this->FILES  = $_FILES;
		$this->GET    = $_GET;
		$this->SERVER = $_SERVER;
	}

	public function __get($key) 
	{
		if( isset($this[$key]) ) return $this[$key];

		if( method_exists( $this, $method = 'get'.ucfirst($key) ) ) 
		{
			return call_user_func( array($this,$method), $key );
		}

		return null;
	}
	public function __set($key, $value)
	{
		$this[$key] = $value;
	}

	public function setUrl( $url )
	{
		$this->url = $url;
	}
	public function setMethod( $method )
	{
		$this->method = $method;
	}

	public function getUrl()
	{
		return  $this->env('REQUEST_URI');
	}

	public function getMethod()
	{
		if(isset($this['method'])) return $this->method;
		if ( isset($this->SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ) {
			return $this->SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}elseif (isset($this->REQUEST['_method'])) {
			return $this->REQUEST['method'];
		}
		return $this->env('REQUEST_METHOD', 'GET');
	}

	private function env($var, $default = '') 
	{
		return isset($this->SERVER[$var]) ? $this->SERVER[$var]: $default;
	}
}
