<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Base Class
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf;

use Seaf\Environment\Environment;
use Seaf\Exception\Exception;

/**
 * Base Class
 */
class Base 
{
    private $env;
    private $root;
    private $config;
    private $isInitialized = false;

    public function __construct( $initializer = false)
    {
        if( $initializer !== false ) 
        {
            $this->init( $initializer );
        }
    }

    public function init( $initializer )
    {
        if( is_array($initializer) )
        {
            $this->env    = $initializer['env'];
            $this->root   = $initializer['root'];
            $this->config = $initializer['config'];
        }

        if( !is_object($this->env) )
        {
            $this->env = new Environment( $this->env );
            $this->env->getFactory('fileLoader')->setParams(
                array( $this->root )
            );
            $this->getConfig( )
                ->setFileLoader($this->getFileLoader())
                ->loadPHPFile( $this->config);
        }

        $this->isInitialized = true;
    }

    public function action( $name, $func ) 
    {
        $this->env->setAction( $name, $func );
    }

    public function filter( $type, $name, $func ) 
    {
        $this->env->addFilter( $type, $name, $func );
    }

    public function before( $name, $func )
    {
        $this->filter( 'before', $name, $func );
    }

    public function after( $name, $func )
    {
        $this->filter( 'after', $name, $func );
    }



    public function __call( $name, $params )
    {
        if( is_callable($this->env->getAction($name)) )
        {
            return $this->env->run( $name, $params );
        }

        $prefix = substr($name, 0, 3);
        if( in_array($prefix, array('get','set','new','del')) )
        {
            return $this->env->component(
                $prefix,
                lcfirst(substr($name, 3)),
                $params
            );
        }
        throw new Exception('invalid method, components or Helper '. $name);
    }
}
