<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * Seaf Environment
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf;

/**
 *
 */
class Environment
{
    private $data = array();
    private $default = array();
   /**
     * construct
     */
    public function __construct($data) 
    {
        $this->data  = $data;

        $this->setDefault('app.root', '/');
        $this->setDefault('app.environment', 'development');
        $this->setDefault('view.templates', $this->get('app.root').'/templates');
    }


    public function get($key, $default = false) 
    {
        if( isset($this->data[$key]) ) return $this->data[$key];

        if( $default == false ) return $this->getDefault($key);

        return $default;
    }

    public function getDefault($key) 
    {
        if( !isset($this->default[$key]) ) return false;

        return $this->default[$key];
    }

    public function setDefault($key, $value) 
    {
        $this->default[$key] = $value;
    }

}
