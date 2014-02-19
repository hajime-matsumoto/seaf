<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * description of Seaf/UI/Container.php
 *
 * File: Seaf/UI/Container.php
 * Created at: 2月 19, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf\UI;
use ReflectionClass;

/**
 * short description of Container
 *
 * long description of Container
 */
class Container
{
	/**
	 * @var array
	 */
	private $factories=array();

	/**
	 * description of instances
	 * @var array instances
	 */
	private $instances=array();

	/**
	 * short description of addFactory
	 *
	 * description of addFactory
	 *
	 * @params $name, $facory, $params, $callback
	 * return $this;
	 */
	public function addFactory($name, $factory, $params = array(),  $callback = false) 
	{
		$this->factories[$name] = array($factory,$params,  $callback);
		return $this;
	}

	/**
	 * Create a instance
	 *
	 * description of newInstance
	 *
	 * @param string $name
	 * return $instance;
	 */
	public function newInstance($name) 
	{
		$factory = $this->getFactory($name);
        $instance = call_user_func($factory);
        return $instance;
	}
	
	/**
	 * short description of getFactory
	 *
	 * description of getFactory
	 *
	 * @params $name
	 * return $newFactory
	 */
	public function getFactory($name) 
	{
		list($factory,$params,$callback) = $this->factories[$name];


		$newFactory = function( ) use ($factory, $params, $callback) {
			if(is_string($factory)) {
				$rc = new ReflectionClass($factory);
				$instance = $rc->newInstanceArgs($params);
			}
			if(is_callable($factory)) {
				$instance = call_user_func_array($factory, $params);
			}
			if(is_callable($callback)){
                $newInstance = call_user_func($callback, $instance, $params);
                if(is_object($newInstance)) {
                    $instance = $newInstance;
                }
			}
			return $instance;
		};


		return $newFactory;
	}

}
