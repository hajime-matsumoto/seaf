<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ベース
 */
namespace Seaf\Base;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Event;
use Seaf\Base;
use Seaf\Logging;

/**
 * 親持ちパターン
 */
trait HasParentTrait
    {
        private $parent;

        public function rootParent ( )
        {
            return $this->findParent(function($o){ return !$o->hasParent();});
        }

        public function findParent (callable $func, $start = true)
        {
            // 開始ではなく、条件に一致すればリターン
            if ($start == false && $func($this)) {
                return $this;
            }

            if ($this->hasParent()) {
                $parent = $this->getParent();
                return $parent->findParent($func, false);
            }

            // 親が居なくなったらFALSE
            return false;
        }

        public function setParent($parent)
        {
            $this->parent = $parent;
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
