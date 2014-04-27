<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

trait SingletonTrait
    {
        protected static $instance;

        public static function who ( ) {
            throw Exception\Exception('whoを実装してください');
        }

        /**
         * シングルトンインスタンスを取得する
         */
        public static function getSingleton ( )
        {
            $class = static::who();
            return isset($class::$instance) ?
                $class::$instance:
                $class::$instance = new $class;
        }

        /**
         * シングルトンインスタンスを交換する
         */
        public function swapSingleton ( )
        {
            $class = static::who();
            $class::$instance = $this;
        }
    }
