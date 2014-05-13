<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\DI;

use Seaf\Util\Util;


/**
 *  DIオーナー
 */
trait DITrait
{
    private $instanceManager;

    protected function initDI( )
    {
        $this->instanceManager = new InstanceManager($this);
        $this->instanceManager->on('instance.create', [$this,'onInstanceCreate']);
        $this->setupDIComponents($this->instanceManager);
    }

    abstract protected function setupDIComponents( );

    public function onInstanceCreate($e) 
    {
        $e->instance;
    }

    public function getComponent($name)
    {
        return $this->instanceManager->getInstance($name);
    }

    public function setComponent($name, $class)
    {
        return $this->instanceManager->register($name, $class);
    }
}
