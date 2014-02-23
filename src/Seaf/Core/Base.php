<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Base Class Definition
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf\Core;

use Seaf\Exception\Exception;

/**
 * Base Class
 *
 * Has A Seaf\Environment\Environment.
 * Controring Environment.
 * Using Environment
 */
class Base 
{
    /**
     * Path To Config From Root
     * @var string
     */
    static public $configPath = 'config.php';

    /**
     * @var object Seaf\Environment\Environment
     */
    private $env;

    /**
     * @var bool
     */
    private $isInitialized = false;


    /**
     * Create Environment Object
     */
    public function __construct( )
    {
        $this->env = new Environment( );
    }

    /**
     * Initialize Environment
     *
     * @param string $root_path 
     * @param string $env {development|production}
     */
    public function init( $root_path = '.', $env = Seaf::ENV_DEVELOPMENT )
    {
        /* Change FileLoader Params */
        $this->env
            ->factory('get', 'fileLoader')
            ->setParams(array( $root_path ) );

        $this->env->setEnvironmentName( $env );

        /* Load Config File */
        $this->getConfig( )
            ->setFileLoader( $this->getFileLoader() )
            ->loadPHPFile( self::$configPath );

        $this->isInitialized = true;
    }

    /**
     * Set Action To Environment
     *
     * @param string $action_name
     * @param mixed $func callback
     */
    public function action( $action_name, $func ) 
    {
        $this->env->action('set', $action_name, $func );
    }

    /**
     * Set Action's Filter
     *
     * @param strint $filter_type
     * @param string $action_name
     * @param mixed $func callback
     */
    public function filter( $filter_type, $action_name, $func ) 
    {
        $this->env->filter('add', $filter_type, $action_name, $func );
    }

    /**
     * Short Hand To Set Action's Befor Filter
     *
     * @param string $action_name
     * @param mixed $func callback
     */
    public function before( $action_name, $func )
    {
        $this->filter( 'before', $action_name, $func );
    }

    /**
     * Short Hand To Set Action's After Filter
     *
     * @param string $action_name
     * @param mixed $func callback
     */
    public function after( $action_name, $func )
    {
        $this->filter( 'after', $action_name, $func );
    }

    public function register( $name, $context, $params = array(), $func = null)
    {
        $this->env->factory(
            'register', $name, $context, $params, $func
        );
    }
    public function comp( $name )
    {
        return $this->env->component('get', $name);
    }
    public function act( $name )
    {
        $args = func_get_args();
        array_shift($args);

        if( is_callable($this->env->action('get', $name)) )
        {
            return $this->env->run( $name, $args );
        }

        throw new Exception(
            'invalid Action '. $name
        );
    }

    /**
     * @param string 
     * @param string
     */
    public function exten( $prefix, $class )
    {
        $self = $this;
        $this->env->factory(
            'register', 
            'ext'.$prefix, 
            $class, 
            array(),
            function($instance) use ($prefix, &$self) {
                $instance->exten( $prefix, $self );
                return $instance;
            }
        );
    }

    /**
     * @param string 
     * @param string
     */
    public function enable( $prefix )
    {
        $extension = $this->env->component('get','ext'.$prefix);
    }

    /**
     * Provids How To Access Environment
     *
     * @param string $called_name
     * @param array $called_params
     */
    public function __call( $called_name, $called_params )
    {
        if( is_callable($this->env->action('get', $called_name)) )
        {
            return $this->env->run( $called_name, $called_params );
        }

        $prefix = substr($called_name, 0, 3);
        if( in_array($prefix, array('get','set','new','del')) )
        {
            return $this->env->component(
                $prefix,
                lcfirst(substr($called_name, 3)),
                $called_params
            );
        }

        /* get component */
        if( $this->env->component('has', $called_name) )
        {
            return $this->env->component('get', $called_name);
        }

        throw new Exception(
            'invalid method, components or Helper '. $called_name
        );
    }
}
