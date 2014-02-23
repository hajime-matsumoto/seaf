<?php

namespace Seaf\Core;

use Seaf\Util\ArrayHelper;

class Dispatcher
{
	/**
	 * @var array
	 */
	private $actions = array();

	/**
	 * @var array
	 */
	private $filters = array();

	/**
	 * Get 
	 */
	public function get( $name )
	{
		return ArrayHelper::get($this->actions, $name, false);
	}

	/**
	 * Set
	 */
	public function set( $name, $func )
	{
		$this->actions[$name] = $func;
	}

	/**
	 * Add Filter
	 */
	public function addFilter( $type, $name, $func )
	{
		$this->filters[$name][$type][] = $func;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param array $params
	 * @param string $output
	 */
	public function runFilter( $name, $type, &$params, &$output )
	{
		if( !isset($this->filters[$name]) ) return $output;
		if( !isset($this->filters[$name][$type])) return $output;


		foreach ( $this->filters[$name][$type] as $func )
		{
			$result = call_user_func_array( 
				$func,
				array( &$params, &$output )
			);
			if($result === false) break;
		}
	}

	public function run( $name, &$params )
	{
		$action = $this->get($name);
		$output = '';

		$this->runFilter($name, 'before', $params, $output );
		$output.= call_user_func_array( $action, $params );
		$this->runFilter($name, 'after', $params, $output );


		return $output;
	}
}
