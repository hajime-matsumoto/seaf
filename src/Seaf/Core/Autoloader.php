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

namespace Seaf\Core;

/**
 * オートローダ
 */
class Autoloader
{
    private static $instance;
    private $namespaces; 
    public static function init( )
    {
        if( self::$instance ) return self::$instance;
        self::$instance = new self();
    }

    public function __construct( )
    {
        $this->namespaces = array(
            'Seaf\\' => realpath(
                dirname(__FILE__).'/../'
            )
        );

        spl_autoload_register(array($this,'seaf'));

        // Seaf Class
    }

    public function seaf($class)
    {
        foreach( $this->namespaces as $ns => $info )
        {
            $dir = $info;

            if( false !== ($p = strpos($class, $ns) ) )
            {
                $file = $dir.'/'.str_replace('\\','/',substr($class,strlen($ns))).'.php';
                if( file_exists($file) )
                {
                    require_once $file;
                    return true;
                }
            }
        }
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
