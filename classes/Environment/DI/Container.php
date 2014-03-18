<?php
namespace Seaf\Environment\DI;

use Seaf\DI;

class Container extends DI\Container
{
    /**
     * @var Container
     */
    private static $singleton;

    // ----------------------------------------------
    // 拡張
    // ----------------------------------------------

    public function create ($name)
    {
        $instance = parent::create($name);

        if (method_exists($instance, 'acceptEnvironment')) {
            $instance->acceptEnvironment($this->owner);
        }
        return $instance;
    }

    // ----------------------------------------------
    // スタティック
    // ----------------------------------------------
    public static function singleton ( )
    {
       return (self::$singleton) ? self::$singleton: self::$singleton = new self();
    }
    public static function who ( )
    {
        return __CLASS__;
    }
}
