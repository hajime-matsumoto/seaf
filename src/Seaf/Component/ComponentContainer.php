<?php
namespace Seaf\Component;

use Seaf\Util\ArrayHelper;
use Seaf\Factory\FactoryContainer;
use Seaf\Component\Exception\ComponentNotFound;

class ComponentContainer
{
	/**
	 * @var object
	 */
	protected $factoryContainer;

	/**
	 * @param object FactoryContainer
	 */
	public function __construct( FactoryContainer $factoryContainer )
	{
		$this->factoryContainer = $factoryContainer;
	}

	/**
	 * Access Component Container Function 
	 *
	 * @param string $action
	 * @param string $name
	 * @param array $params
	 */
	public function factory( $action, $name, $params = array() )
	{
		return call_user_func_array(
			array(
				$this->factoryContainer,
				$action
			),
			array($name) + $params
		);
	}

	/**
	 * Check if it has
	 *
	 * @param string $name
	 */
	public function hasComponent( $name )
	{
		return 
			isset($this->components[$name]) ||
			$this->factories->has($name);
	}

	/**
	 * Get Component
	 *
	 * @param string $name
	 */
	public function getComponent( $name )
	{
		if( isset($this->components[$name]) ) return $this->components[$name];

		return $this->components[$name] = $this->newComponent( $name );
	}

	/**
	 * New Component
	 *
	 * @param string $name
	 */
	public function newComponent( $name )
	{
		if( !$this->factoryContainer->has($name) ) 
		{
			throw new ComponentNotFound($name);
		}
		return $this->factoryContainer->get($name)->invoke();
	}

	/**
	 * Set Component
	 *
	 * @param string $name
	 * @param object 
	 */
	public function setComponent( $name, $object )
	{
		$this->components[$name] = $object;
	}

	/**
	 * Delete Component
	 *
	 * @param string $name
	 * @param object 
	 */
	public function delComponent( $name )
	{
		unset($this->components[$name]);
	}
}
