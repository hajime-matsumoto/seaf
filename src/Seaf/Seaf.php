<?php
/* vim: set expandtab ts=4 sw=4 sts=4: et*/

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Main Class Of Seaf
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf;

use Seaf\Core\Base;


class Seaf
{
    const ENV_DEVELOPMENT='development';
    const ENV_PRODUCTION='production';

    /**
     * Singleton Instance
     * @var object
     */
    static private $instance;

    /**
     * Seaf::Base
     * @var object
     */
    private $base;

    /**
     * construct for singleton
     */
    private function __construct() 
    {
        $this->base = new Base();
    }

    /**
     * Get Singleton Instance
     */
    static public function getInstance () 
    {
        if (self::$instance) return self::$instance;
        self::$instance = new Seaf();
        return self::$instance;
    }

    /**
     * When It Is Called, Delegated Call To $base 
     *
     * @param name  $name 
     * @param array  $params params
     * @return mixed
     */
    static public function __callStatic($name, array $params = array()) 
    {
        $seaf = self::getInstance();
        return call_user_func_array(array($seaf->base, $name), $params);
    }
}
