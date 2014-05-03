<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Singleton;

/**
 * シングルトン
 */
trait SingletonTrait
    {
        protected static $instances;

        /**
         * シングルトンインスタンスを取得する
         */
        public static function getSingleton ($name = 'default', $config = [])
        {
            $class = static::who();

            return (isset($class::$instances[$name]) && empty($config)) ?
                $class::$instances[$name]:
                $class::$instances[$name] = new $class ($config);
        }

        /**
         * シングルトンインスタンスを交換する
         */
        public function registerSingleton ($name = 'default')
        {
            $class = static::who();
            $class::$instances[$name] = $this;
        }
    }
