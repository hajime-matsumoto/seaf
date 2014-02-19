<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * description of Seaf/Extension/ExtensionManager.php
 *
 * File: Seaf/Extension/ExtensionManager.php
 * Created at: 2月 19, 2014
 *
 * @copyright Copyright (c) 2014, Hajime MATSUMOTO <mail@hazime.org>
 * @license   MIT, http://mail@hazime.org
 */
 
namespace Seaf\Extension;

use Seaf\UI\Container as UIContainer;

/**
 * short description of ExtensionManager
 *
 * long description of ExtensionManager
 */
class ExtensionManager
{
    /**
     * description of $base
     * @var mixed $base
     */
    private $base;
    
    /**
     * description of uicontainer
     * @var mixed uicontainer
     */
    protected $uicontainer;

    /**
     * short description of __construct
     *
     * description of __construct
     *
     * @params 
     * return null
     */
    public function __construct() 
    {
        $this->uicontainer = new UIContainer();
        return null;
    }
    
    /**
     * short description of setSeafBase
     *
     * description of setSeafBase
     *
     * @params $base
     * return $this;
     */
    public function setSeafBase($base) 
    {
        $this->base = $base;
        return $this;
    }

    /**
     * short description of initialize
     *
     * description of initialize
     *
     * @params 
     * return $this;
     */
    public function initialize() 
    {
        // Baseにextensionのメソッドを追加する
        $this->base->setHelpers(array(
            'exten' => 'register',
            'enable' => 'enable'
        ), $this);
        return $this;
    }

    /**
     * Register Factories
     *
     * description of register
     *
     * @params $name, $factory, $callback
     * return $this;
     */
    public function register($name, $factory, $params = array(), $callback = false) 
    {
        $this->uicontainer->addFactory( $name, $factory, $params = array(), $callback);
        return null;
    }

    /**
     * short description of enable
     *
     * description of enable
     *
     * @params $name
     * return null;
     */
    public function enable($name) 
    {
        $ext = $this->uicontainer->getInstance($name);
        $ext->enable($this->base);
        return null;
    }
}
