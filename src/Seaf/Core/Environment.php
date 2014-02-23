<?php

namespace Seaf\Core;

use Seaf\Factory\FactoryContainer;
use Seaf\Component\ComponentContainer;

/**
 * Seaf Environment.
 *
 * Controlling Action And Objects.
 * Work As UI Container Also Method Dispatcher.
 */
class Environment
{
	/**
	 * Environment Name
	 * @var string
	 */
	private $envName;

	/**
	 * Component Container
	 * @var object
	 */
	private $componentContainer;

	/**
	 * Action Dispatcher
	 * @var object
	 */
	private $actionDispatcher;


	/**
	 * Construct Environment
	 */
	public function __construct( )
	{
		$this->componentContainer = new ComponentContainer( 
			new FactoryContainer(
				array(
					'config'     => 'Seaf\Config\Config',
					'fileLoader' => 'Seaf\Loader\FileSystemLoader'
				)
			)
		);

		$this->actionDispatcher = new Dispatcher( );

		$this->action('set','stop',function($body){
			exit($body);
		});
	}

	/**
	 * Set Environment Name
	 *
	 * @param string 
	 */
	public function setEnvironmentName( $env_name )
	{
		$this->envName = $env_name;
	}

	/**
	 * Access Factory Container Function 
	 *
	 * @param string $action
	 */
	public function factory( $action )
	{
		$args = func_get_args();
		return call_user_func_array(
			array(
				$this->componentContainer,
				'factory'
			), $args
		);
	}

	/**
	 * Access Component Container Function 
	 *
	 * @param string $action
	 * @param string $name
	 * @param array $params
	 */
	public function component( $action, $name, $params = array() )
	{
		return call_user_func_array(
			array(
				$this->componentContainer,
				$action.'Component'
			),
			array($name) + $params
		);
	}

	/**
	 * Access Action Dispatcher  Function 
	 *
	 * @param string $action
	 */
	public function action( $action )
	{
		$args = func_get_args();
		array_shift($args);

		return call_user_func_array(
			array(
				$this->actionDispatcher,
				$action
			),
			$args
		);
	}

	/**
	 * Access Action Dispatcher Filter Function 
	 *
	 * @param string $action
	 */
	public function filter( $action )
	{
		$args = func_get_args();
		array_shift($args);
		return call_user_func_array(
			array(
				$this->actionDispatcher,
				$action.'Filter'
			),
			$args
		);
	}


	public function run( $name, &$params )
	{
		return $this->actionDispatcher->run($name, $params);
	}
}
