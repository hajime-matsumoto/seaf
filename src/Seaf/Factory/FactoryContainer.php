<?php

namespace Seaf\Factory;

use Seaf\Util\ArrayHelper;


class FactoryContainer
{
	/**
	 * @var object
	 */
	private $factories;

	public function __construct( $factories = array() )
	{
		if( !empty($factories) ) 
		{
			$this->register( $factories );
		}
	}

	/**
	 * Register Factory
	 *
	 * @param mixed $name 
	 * @param mixed $context
	 * @param mixed $params
	 * @param mixed $callback
	 */
	public function register( 
		$name, $context = null, $params = array(), $callback = false
	)
	{
		if( $context == null && is_array($name) )
		{
			$factories = $name;
			foreach( $factories as $key => $params )
			{
				if( !is_array($params) ) $params = array($params);
				array_unshift( $params, $key );
				call_user_func_array(
					array($this,'register'), $params
				);
			}
		}
		else
		{
			$this->factories[$name] = $this->createFactory(
				$context, $params, $callback
			);
		}
	}

	/**
	 * Create Factory
	 *
	 * @param $name 
	 * @param mixed $context
	 * @param array $params
	 * @param mixed $callback
	 */
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

	/**
	 * Get A Factory
	 *
	 * @param string $name
	 * @return bool object
	 */
	public function get( $name )
	{
		return ArrayHelper::get($this->factories, $name, false);
	}


	/**
	 * Has A Factory
	 *
	 * @param string $name
	 * @return bool
	 */
	public function has( $name )
	{
		return isset($this->factories[$name]);
	}

}
