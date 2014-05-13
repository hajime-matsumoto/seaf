<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * コンポーネント
 */
namespace Seaf\Base\Component;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\Base;
use Seaf\Logging;

/**
 * モジュールファサード
 */
trait ComponentTrait
    {
        use Logging\LoggableTrait;
        use Event\ObservableTrait;
        use Base\HasParentTrait;
        use Base\TaggableTrait;

        public function setParentComponent(ComponentIF $component)
        {
            $this->addObserver($component);
            $this->setParent($component);
            $this->tag('parent_name', $component->getObjectName());
        }

        public function module($name)
        {
            return $this->findParent(function($p) use ($name){
                if (method_exists($p,'hasModule')) {
                    if( $p->hasModule($name) ) {
                        $this->debug(['Found %s in %s',$name,get_class($p)]);
                        return true;
                    }
                }
                return false;
            })->$name;
        }

        public function getObjectName()
        {
            $name = $this->getTag('object_name', static::$object_name);

            if ($this->hasTag('parent_name')) {
                return strtoupper($this->getTag('parent_name').'|'.$name);
            }
            return strtoupper($name);
        }
    }
