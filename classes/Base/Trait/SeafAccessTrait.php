<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

use Seaf\Core\Seaf;

trait SeafAccessTrait
    {
        public function seaf() 
        {
            return Seaf::singleton( );
        }

        /**
         * ショートハンド
         */
        public function sf() 
        {
            return $this->seaf();
        }
    }
