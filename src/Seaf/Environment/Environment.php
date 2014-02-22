<?php

namespace Seaf\Environment;

use Seaf\Factory\FactoryClassName;
use Seaf\Factory\FactoryCallback;

class Environment
{
	private $env;
	private $config;
	private $components = array();

	private $filters = array();
	private $actions = array();

	/**
	 * @param string $env
	 * @param object $config
	 */
	public function __construct( $env = 'development', $config  = false)
	{
		$this->env = $env;
		$this->config = $config;

		$this->regComponent( 'config', 'Seaf\Config\Config' );
		$this->regComponent( 'fileLoader', 'Seaf\Loader\FileSystemLoader' );
	}

	public function component( $action, $name, $params )
	{
		return call_user_func_array(
			array(
				$this,
				$action.'Component'
			),
			array($name) + $params
		);
	}

	public function setAction( $name, $func )
	{
		$this->actions[$name] = $func;
	}

	public function getAction( $name )
	{
		if( isset($this->actions[$name]) ) return $this->actions[$name];
		return false;
	}

	public function run( $name, &$params )
	{
		$action = $this->getAction($name);
		if(!isset($this->filters[$name])) $this->filters[$name] = array();
		$filters = $this->filters[$name];
		$output = '';


		if( isset($filters['before']) ) 
			foreach ( $filters['before'] as $func )
			{
				$result = call_user_func_array( 
					$func,
					array( &$params, &$output )
				);
				if($result === false) break;
			}

		$output.= call_user_func_array( $action, $params );

		if( isset($filters['after']) ) 
			foreach ( $filters['after'] as $func )
			{
				$result = call_user_func_array( 
					$func,
					array( &$params, &$output )
				);
				if($result === false) break;
			}
		return $output;
	}


	public function addFilter( $type, $name,  $func )
	{
		$this->filters[$name][$type][] = $func;
	}

	public function getComponent( $name )
	{
		if( isset($this->components[$name]) ) return $this->components[$name];

		return $this->components[$name] = $this->newComponent( $name );
	}

	public function newComponent( $name )
	{
		if( !isset($this->factories[$name]) ) throw new ComponentNotFoune($name);
		return $this->factories[$name]->invoke();
	}

	public function setComponent( $name, $object )
	{
		$this->components[$name] = $object;
	}

	public function regComponent( $name, $factory )
	{
		if( !is_object($factory) ) $factory = $this->createFactory($factory);
		$this->delComponent( $name );
		$this->factories[$name] = $factory;
	}

	public function delComponent( $name )
	{
		unset($this->components[$name]);
	}

	public function createFactory( $context, $params = array(), $callback = false )
	{
		if( is_string($context) )
		{
			$factory = new FactoryClassName($context);
		}
		elseif( is_callable( $contect ) )
		{
			$factory = new FactoryCallback($context);
		}
		$factory->setParams( $params );
		$factory->setCallback( $callback );
		return $factory;
	}

	public function getFactory( $name )
	{
		return $this->factories[$name];
	}

}
