<?php

namespace Seaf\Net;

use Seaf\Core\Extension;
use Seaf\Core\Base;

class WebExtension extends Extension
{
	public function init( $prefix, $base )
	{
		$this->register('request' , 'Seaf\Net\Request');
		$this->register('response', 'Seaf\Net\Response');
		$this->register('router'  , 'Seaf\Net\Router');
	}

	public function actionRoute( $route, $func = null )
	{
		if( $func == null && is_array($route) )
		{
			foreach( $route as $k => $v )
			{
				$this->actionRoute( $k, $v );
			}
		}
		else
		{
			$this->comp('router')->map($route, $func);
		}
	}

	public function actionMap( $patterm, $func )
	{
		$this->comp('router')->map( $patterm, $func );
	}

	public function actionStart( )
	{
		$dispatched = false;
		$self   = $this;
		$req    = $this->request;
		$res    = $this->response;
		$router = $this->router;

		if( ob_get_length() > 0 )
		{
			$res->write(ob_get_clean());
		}

		ob_start();

		$this->after('start', function() use ($self) {
			$self->act('stop');
		});

		// Route the request
		while ($route = $router->route($req)) 
		{
			$params = array_values($route->params);
			array_push($params, $route);

			$continue = call_user_func_array(
				$route->callback,
				$params
			);

			$dispatched = true;

			if (!$continue) break;

			$router->next();
		}

		if (!$dispatched) {
			$this->act('notFound');
		}
	}

	public function actionStop( $code = 200 )
	{
		$this->response
			->status( $code )
			->write( ob_get_clean() )
			->send( );
	}

	public function actionNotFound( )
	{
		$this->response
			->status(404)
			->write(
				'<h1>404 Not Found</h1>'.
				'<section style="padding:10px">URL:'.$this->request->url.'</section>'
				.str_repeat(' ', 512)
			)->send();
	}

	public function actionHalt( $message, $code = 200 )
	{
		$this->response
			->status( $code )
			->write( $message )
			->send( );
	}
}
