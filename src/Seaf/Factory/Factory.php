<?php

namespace Seaf\Factory;


class Factory
{
	protected $context;
	protected $params;
	protected $callback;

	public function __construct( $context )
	{
		$this->context = $context;
	}

	public function setParams( $params )
	{
		$this->params = $params;
	}

	public function setCallback( $callback )
	{
		$this->callback = $callback;
	}

	public function invoke( )
	{
		$instance = $this->createInstance();
		if( is_callable($this->callback) ) 
		{
			call_user_func(
				$this->callback, $instance
			);
		}
		return $instance;
	}
}
