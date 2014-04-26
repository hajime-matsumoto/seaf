<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Registry;

use Seaf\Base;
use Seaf\Container;

class Registry extends Container\ArrayContainer
{
    use Base\SingletonTrait;

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

    public function shutdown ( )
    {
        foreach ($this->shutdownFunctions as $func) {
            call_user_func($func);
        }
    }
}
