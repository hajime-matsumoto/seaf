<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

trait SingletonTrait
    {
        private static $instance;

        abstract public static function who ( );

        /**
         * シングルトンインスタンスを取得する
         */
        public static function getSingleton ( )
        {
            $class = static::who();
            return isset(static::$instance) ?
                static::$instance:
                static::$instance = new $class;
        }

    }
