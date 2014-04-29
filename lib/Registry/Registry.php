<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Registry;

use Seaf\Base;
use Seaf\Container;
use Seaf\Logging;
use Seaf\Event;

class Registry extends Container\ArrayContainer
{
    use Base\SingletonTrait;
    use Logging\LoggingTrait;
    use Event\ObservableTrait;

    private $shutdownFunctions = [];

    public function __construct($data = [])
    {
        parent::__construct($data);
        register_shutdown_function([$this, 'shutdown']);
    }

    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * @return bool
     */
    public static function isProduction ( )
    {
        if (static::getSingleton( )->getVar('env') === 'production') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function isDebug ( )
    {
        if (static::getSingleton( )->getVar('env') === 'production') {
            return false;
        }

        return (bool) static::getSingleton( )->getVar('isDebug', false);
    }

    /**
     * @return bool
     */
    public static function enableDebug ( )
    {
        static::getSingleton( )->trigger('enable.debug');
        static::getSingleton( )->setVar('isDebug', true);
    }

    /**
     * @return bool
     */
    public static function disableDebug ( )
    {
        static::getSingleton( )->trigger('disable.debug');
        static::getSingleton( )->setVar('isDebug', false);
    }

    public static function registerShutdownFunction($func)
    {
        static::getSingleton()->shutdownFunctions[] = $func;
    }

    public static function unRegisterShutdownFunction($func)
    {
        foreach (static::getSingleton()->shutdownFunctions as $key => $registeredFunc) {
            if ($func == $registeredFunc) {
                unset(static::getSingleton()->shutdownFunctions[$key]);
            }
        }
    }

    public static function regGet ($key, $default = null)
    {
        return static::getSingleton()->getVar($key, $default);
    }

    public static function regGetLast ($key)
    {
        $array = static::getSingleton()->getVar($key, []);
        return array_pop($array);
    }

    public static function regGetClear ($key, $default = null)
    {
        return static::getSingleton()->getVarClear($key, $default);
    }

    public static function regSet ($key, $value)
    {
        return static::getSingleton()->setVar($key, $value);
    }

    public function shutdown ( )
    {
        foreach ($this->shutdownFunctions as $func) {
            call_user_func($func);
        }
    }
}
