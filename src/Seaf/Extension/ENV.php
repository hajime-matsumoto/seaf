<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * description of Seaf/Extension/ENV.php
 *
 * File: Seaf/Extension/ENV.php
 * Created at: 2月 20, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */

namespace Seaf\Extension;

/**
 * short description of ENV
 *
 * long description of ENV
 */
class ENV extends Extension
{
	/**
	 * description of data
	 * @var mixed data
	 */
	private $data=array();
	
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
		$self = $this;

		$base->register('env', function()use($self){return $self;});

        // BaseにHello Worldを追加
        $base->setHelpers(array(
            'envGet' => 'getVar',
            'envSet' => 'setVar'
        ), $this);

        return $this;
	}

	/**
	 * short description of getVar
	 *
	 * description of getVar
	 *
	 * @params $key, $default = null
	 * return $this;
	 */
	public function getVar($key, $default = null) 
	{
		return isset($this->data[$key]) ? $this->data[$key]: $default;
	}

	/**
	 * short description of setVar
	 *
	 * description of setVar
	 *
	 * @params $name, $value
	 * return $this
	 */
	public function setVar($key, $value) 
	{
		$this->data[$key] = $value;
		
		return $this;
	}
}
