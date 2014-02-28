<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Router;


class Route
{
    /**
     * pattern
     * @var string
     */
    private $pattern;

    /**
     * @var mixed
     */
    public $callback;

    /**
     * @var string
     */
    private $methods;

    /**
     * @var array
     */
    public $params = array();

    /**
     * @var string
     */
    public $regex;

    /**
     * @var string
     */
    public $splat;


    /**
     * @param string
     * @param closure
     * @param array
     */
    public function __construct( $pattern, $callback, $methods ) 
    {
        $this->pattern = $pattern;
        $this->callback = $callback;
        $this->methods = $methods;
        $this->params = array();
    }

    public function getCallback( )
    {
        $callback = $this->callback;
        $params = $this->params;

        return function() use( $callback, $params ) {
            return call_user_func_array( $callback, $params );
        };
    }

    /**
     * @param string
     * @return bool
     */
    public function matchMethod( $method ) 
    {
        return count(
            array_intersect(
                array($method, '*'),
                $this->methods
            )
        ) > 0;
    }



    /**
     * @param string
     * @return bool
     */
    public function matchUrl($url) 
    {
        if( $this->pattern === "*" || $this->pattern === $url ) return true;

        $ids = array();
        $char = substr($this->pattern, -1);
        $this->splat = substr($url, strpos($this->pattern, '*'));

        $this->pattern = str_replace(
            array(')','*'), 
            array(')?','.*?'),
            $this->pattern
        );

        $regex = preg_replace_callback(
            '#@([\w]+)(:([^/\(\)]*))?#',
            function($matches) use (&$ids){
                $ids[$matches[1]] = null;
                if( isset($matches[3]) ){
                    return '(?P<'.$matches[1].'>'.$matches[3].')';
                }
                return '(?P<'.$matches[1].'>[^/\?]+)';
            }, $this->pattern
            );

        if ($char === '/' ) 
        {
            $regex .= '?';
        }
        else
        {
            $regex .= '/?';
        }

        if( preg_match('#^'.$regex.'(?:\?.*)?$#i', $url, $matches) )
        {
            foreach ($ids as $k=>$v) 
            {
                $this->params[$k] = 
                    (array_key_exists($k, $matches)) ? urldecode($matches[$k]): null;
            }
            $this->regex = $regex;
            return true;
        }
        return false;
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
