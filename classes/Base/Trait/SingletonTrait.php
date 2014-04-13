<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

trait SingletonTrait
    {
        protected static $instance = false;

        abstract protected static function who ( );

        public static function singleton ( )
        {
            $class = static::who();
            if ($class::$instance) {
                return $class::$instance;
            } else {
                return $class::$instance = new $class();
            }
        }
    }
