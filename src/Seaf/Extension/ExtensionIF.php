<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * ExtensionInterFace
 *
 * File: Seaf/Extension/ExtensionIF.php
 * Created at: 2月 19, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf\Extension;

/**
 * ExtensionIF
 *
 * long description of ExtensionIF
 */
interface ExtensionIF
{
    /**
     * エクステンションを有効化する
     */
    public function enable($base);
	
}
