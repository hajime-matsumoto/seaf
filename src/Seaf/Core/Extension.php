<?php

namespace Seaf\Core;
use Seaf\Core\Base;

abstract class Extension
{
	/**
	 * @var object
	 */
	private $base;
	private $prefix;

	public function exten( $prefix, Base $base )
	{
		$this->base = $base;
		$this->prefix = $prefix;

		foreach(get_class_methods($this) as $action) 
		{
			if( strpos($action, 'action') === 0 )
			{
				$action_name = substr($action, 6);
				$base->action(
					$prefix.$action_name,
					array($this, $action)
				);
			}
			if( strpos($action, 'map') === 0 )
			{
				$action_name = substr($action, 3);
				$base->map(
					$prefix.$action_name,
					array($this, $action)
				);
			}
		}

		$this->init( $prefix, $base );
	}

	abstract protected function init( $prefix, $base );


	public function base( )
	{
		return $this->base;
	}

	public function prefix($name)
	{
		return $this->prefix.ucfirst($name);
	}


	public function register( $name, $context, $params=array(), $func = null ){
		$this->base->register(
			$this->prefix($name),
			$context,
			$params,
			$func
		);
	}

	public function after( $name, $func )
	{
		$this->base->after( $this->prefix($name), $func);
	}

	public function before( $name, $func )
	{
		$this->base->before( $this->prefix($name), $func);
	}

	public function comp($name)
	{
		return $this->base->comp( $this->prefix($name) );
	}

	public function act($name)
	{
		return $this->base->act( $this->prefix($name) );
	}
    public function map( $name, $func )
    {
        return $this->base->map( $this->prefix($name), $func);
    }

	public function action($name, $func)
	{
		return $this->base->action( $this->prefix($name), $func );
	}

	public function __get($name)
	{
		return $this->comp($name);
	}

	public function __call( $name, $params )
	{
		if(	
			is_callable($this->base->env()->getMethod( $this->prefix($name)))
		){
			$name = $this->prefix($name);
		}
		else if( 
			is_callable($this->base->env()->action( 'get', $this->prefix($name)))
		){
			$name = $this->prefix($name);
		}
		return call_user_func_array(
			array(
				$this->base,
				$name
			), $params
		);
	}
}
