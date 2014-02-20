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
   /**
     * construct
     */
    public function __construct($data) 
    {
		$this->data  = $data;
    }
}
