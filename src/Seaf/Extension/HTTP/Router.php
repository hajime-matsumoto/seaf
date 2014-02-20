<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * @copyright Copyright (2) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Extension\HTTP;

class Router
{
	private $index = 0;
	protected  $routes = array();

	public function route( Request $request ) {
		while ($route = $this->current()) {
			if( 
				$route !== false &&
				$route->matchMethod($request->method()) && 
				$route->matchUrl($request->url()) ) {
				return $route;
			}
			$this->next();
		}
		return false;
	}

	public function map( $pattern, $callback ){
		if (strpos($pattern, ' ') !== false) {
			list($method, $url) = explode( ' ', trim($pattern), 2);
			$methods = explode( '|', $method);
			array_push($this->routes, new Route($url, $callback, $methods));
		}else{
			array_push($this->routes, new Route($pattern, $callback, array('*')));
		}
	}

	public function current(){
		return isset($this->routes[$this->index]) ? $this->routes[$this->index]: false;
	}
	public function next(){
		$this->index++;
	}
	public function reset(){
		$this->index = 0;
	}
}
