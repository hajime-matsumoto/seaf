<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * description of Seaf/Extension/TestExtension.php
 *
 * File: Seaf/Extension/TestExtension.php
 * Created at: 2月 19, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf\Extension;

/**
 * Use Only Test
 *
 * long description of TestExtension
 */
class TestExtension extends Extension
{
	/**
	 * short description of enable
	 *
	 * description of enable
	 *
	 * @params 
	 * return null
	 */
	public function enable($base) 
	{
		parent::enable($base);
		$this->initialize($base);
		return null;
    }

    /**
     * short description of initialize
     *
     * description of initialize
     *
     * @params 
     * return $this;
     */
    protected function initialize($base) 
    {
        // BaseにHello Worldを追加
        $base->setHelpers(array(
            'echoHelloWorld' => 'echoHelloWorld'
        ), $this);

        $base->setActions(array(
            'retHelloWorld' => 'retHelloWorld'
        ), $this);
        return $this;
    }

    public function echoHelloWorld( ) {
        echo 'Hello World';
    }
    public function retHelloWorld( ) {
        return 'Hello World';
    }
}
