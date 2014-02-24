<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Net;

class Router
{
	/**
	 * @var int
	 */
	private $index = 0;

	/**
	 * @var array
	 */
	protected  $routes = array();

	/**
	 * Matching
	 *
	 * @param object $request
	 */
	public function route( Request $request ) 
	{
		while ($route = $this->current()) 
		{
			if( 
				$route !== false &&
				$route->matchMethod($request->method) &&
				$route->matchUrl($request->url) )
			{
				return $route;
			}
			$this->next();
		}
		return false;
	}

	/**
	 * Create A Route
	 *
	 * @param string
	 * @param callback 
	 */
	public function map( $pattern, $callback )
	{
		if (strpos($pattern, ' ') !== false) 
		{
			list($method, $url) = explode( ' ', trim($pattern), 2);
			$methods = explode( '|', $method);
			array_push($this->routes, new Route($url, $callback, $methods));
		}
		else
		{
			array_push($this->routes, new Route($pattern, $callback, array('*')));
		}
	}

	public function current()
	{
		return 
			isset($this->routes[$this->index]) ? 
				$this->routes[$this->index]:
				false;
	}

	public function next()
	{
		$this->index++;
	}

	public function reset()
	{
		$this->index = 0;
	}
}
