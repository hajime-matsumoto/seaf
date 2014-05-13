<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * オブジェクト関係
 */
namespace Seaf\Base\Object;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\Base\Object;
use Seaf\Logging;

/**
 * モジュールファサード
 */
trait CompositeTrait
    {
        use Logging\LoggableTrait;
        use Event\ObservableTrait;
        use Object\CompositeTrait;

        public function root ( )
        {
            if ($this->parent) {
                return $this->parent->root();
            }else{
                return $this;
            }
        }

        public function setParent($parent)
        {
            $this->parent = $facade;
        }

        public function getParent( )
        {
            return $this->parent;
        }

        public function hasParent( )
        {
            return empty($this->parent) ? false: true;
        }
    }
