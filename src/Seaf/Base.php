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
use Seaf\UI\Container as UIContainer;

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
     * description of uicontainer
     * @var mixed uicontainer
     */
    protected $uicontainer;

    /**
     * description of helpers
     * @var mixed helpers
     */
    protected $helpers=array();
    

	/**
	 * constructor
	 */
	public function __construct() 
	{
        $this->dispatcher = new Dispatcher();
        $this->uicontainer = new UIContainer();
        $this->builtinActions = array();
        $this->initialize();
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
        $this->initialize();
        return null;
    }

    /**
     * initialize base
     */
    protected function initialize()
    {
        $self = $this; 

        $this->dispatcher->init();
        $this->uicontainer->init();

        $this->register('extension', 'Seaf\Extension\ExtensionManager', function( $instance ) use($self){
            $instance->setSeafBase($self);
            $instance->initialize();
            $self->exten('test', 'Seaf\Extension\TestExtension');
            $self->exten('http', 'Seaf\Extension\HTTP\Http');
        });

        foreach ($this->builtinActions as  $name) {
            $this->dispatcher->setMethod( $name, array($this, 'action'.ucfirst($name)) );
        }
    }

    /**
     * Register Factories
     *
     * description of register
     *
     * @params $name, $factory, $callback
     * return $this;
     */
    public function register($name, $factory, $params = array(), $callback = false) 
    {
        $this->uicontainer->addFactory( $name, $factory, $params, $callback );
        return $this;
    }

    /**
     * Set Helper
     *
     * description of setHelper
     *
     * @params $key, $func = false
     * return $this;
     */
    public function setHelper($key, $func = false) 
    {
        if(is_array($key)) return $this->setHelpers($key, $func);
        $this->helpers[$key] = $func;
        return $this;
    }

    /**
     * short description of setHelpers
     *
     * description of setHelpers
     *
     * @params $map, $object = false
     * return null
     */
    public function setHelpers($map, $object = false) 
    {
        foreach($map as $key=>$func) {
            if(is_object($object)) {
                $this->setHelper($key, array($object, $func));
            }
        }
        return $this;
    }

    /**
     * Set Action
     *
     * description of setAction
     *
     * @params $key, $func = false
     * return $this;
     */
    public function setAction($key, $func = false) 
    {
        if(is_array($key)) return $this->setActions($key, $func);
        $this->dispatcher->setMethod($key, $func);
        return $this;
    }

    /**
     * short description of setActions
     *
     * description of setActions
     *
     * @params $map, $object = false
     * return null
     */
    public function setActions($map, $object = false) 
    {
        foreach($map as $key=>$func) {
            if(is_object($object)) {
                $this->setAction($key, array($object, $func));
            }
        }
        return $this;
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

    public function execute( $func, $params ){
        return $this->dispatcher->execute($func, $params);
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
        if(isset($this->helpers[$name])) {
            return call_user_func_array($this->helpers[$name], $params);
        }

		$callback = $this->dispatcher->getMethod($name);

		if (is_callable($callback)){
		  return $this->dispatcher->run($name, $params);
        }

        return $this->uicontainer->getInstance($name);
	}
}
