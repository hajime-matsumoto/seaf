<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Base Class
 *
 * Base Class
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf;

/**
 * Base Class
 *
 * Base Class Of Seaf
 */

class Base 
{
	/**
	 * Dispatchar
	 * @var object
	 */
    private $dispatcher;

    /**
     * @var array 
     */
    protected $builtinActions;

	/**
	 * constructor
	 */
	public function __construct() 
	{
        $this->dispatcher = new Dispatcher();
        $this->builtinActions = array('helloWild','start');
        $this->initialize();
    }

    /**
     * initialize base
     */
    protected function initialize()
    {
        foreach ($this->builtinActions as  $name) {
            $this->dispatcher->setMethod( $name, array($this, 'action'.ucfirst($name)) );
        }
    }

    /**
     * create hook
     */
    public function after($name, $callback) 
    {
        $this->dispatcher->hook($name, 'after', $callback);
    }
	
    /**
     * create hook
     */
    public function before($name, $callback) 
    {
        $this->dispatcher->hook($name, 'before', $callback);
    }

    /**
     */
    public function actionHelloWild($name)
    {
        return "hello wild ".$name;
    }


	/**
	 * Catch All Method Call
	 *
	 * Dispatcherを使ってメソッドをディスパッチする
	 *
	 * @param string $name 
	 * @retun mixed 
	 */
	public function __call($name, array $params = array())
	{
		$callback = $this->dispatcher->getMethod($name);

		if (is_callable($callback)){
		  return $this->dispatcher->run($name, $params);
		}
	}
	
	
}
    
    
