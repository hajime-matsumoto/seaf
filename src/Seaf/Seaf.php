<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Define Seaf
 *
 * Seaf class file
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf;

/**
 * Seaf機能へのアクセスを提供するクラス
 *
 * このクラスのメソッド、プロパティは全てベースクラスによって
 * 動的に定義されます。
 *
 */

class Seaf
{
    /**
     * @var object
     */
    static private $instance;

    /**
     * Base
     * @var object
     */
    static private $base;
    
    /**
     * Static
     *
     * All Static Call delegate to base
     *
     * @param name  $name 
     * @param array  $params params
     * @return mixed
     */
    static public function __callStatic($name, array $params = array()) 
    {
        $seaf = self::getInstance();
        array_push($params, $seaf);
        return call_user_func_array(array($seaf->base, $name), $params);
    }

    /**
     * Get Singleton Instance
     */
    static public function getInstance () 
    {
        if (self::$instance) {
            return self::$instance;
        }
        self::$instance = new Seaf();
        return self::$instance;
    }

    /**
     * construct
     */
    private function __construct() 
    {
        $this->base = new Base();
    }
}
