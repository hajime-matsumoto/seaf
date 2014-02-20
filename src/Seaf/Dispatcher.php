<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Dispatcher
 *
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf;

/**
 * Dispatcher
 */
class Dispatcher 
{
	/**
	 * @var array
	 */
	public $methods = array();

	/**
	 * @var array
	 */
	protected $filters = array();

	/**
	 * constructor
	 */
	public function __construct() 
	{
		
    }

    /**
     * short description of init
     *
     * description of init
     *
     * @params 
     * return null
     */
    public function init() 
    {
        $this->methods = array();
        $this->filters = array();
        return null;
    }


	/**
	 * Method Setter
	 *
	 * @param string $name
	 * @param callback $callback
	 */
    public function setMethod( $name, $callback ) {
		$this->methods[$name] = $callback;
	}

	/**
	 * Method Getter
	 *
	 * Getting a Method
	 *
	 * @param string $name 
	 * @retun callable
	 */
	public function getMethod($name)
	{
		if (!$this->hasMethod($name)) {
			return false;
		}
		return $this->methods[$name];
	}

	/**
	 * if method exist
	 *
	 * description
	 * @param string $name
	 * @retun bool
	 */
	public function hasMethod($name)
	{
		return isset($this->methods[$name]);
	}

	/**
	 * Run Method
	 *
	 * @param string $name 
	 * @param array $params
	 * @retun mixed
	 */
	public function run($name, $params = array())
	{
		$output = '';

		// Run pre-filter
		$this->filter($name.':before', $params, $output);

		// Run requested method
		$output.= $this->execute( $this->getMethod($name), $params );

		// Run after-filter
		$this->filter($name.':after', $params, $output);

		return $output;
		
	}

	/**
	 * filter
	 *
	 * @param string $name
	 * @param string $type
	 * @param callable $callback
	 */
	public function hook($name, $type, $callback){
		$this->filters[$name.':'.$type][] = $callback;
	}

	/**
	 * execute filter
	 *
	 * @param string $name
	 * @param array $params
	 * @param array $output
	 */
	public function filter( $name, &$params, &$output ) {
        $args = array(&$params, &$output);
		if( empty($this->filters[$name]) ) return;

		foreach($this->filters[$name] as $callback) {
			$continue = $this->execute($callback, $args);
			if ($continue === false) break;
		}
	}

	/**
	 * dispaatch method
	 *
	 * @param callable $callback 
	 * @retun mixed
	 */
	public function execute($callback, $args)
	{
		if( !is_callable($callback) ) {
			throw new InvalidCallback('invalid callback'); 
		}
		return call_user_func_array($callback, $args);
	}
}
