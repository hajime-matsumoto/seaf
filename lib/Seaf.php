<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

use Seaf\Component;
use Seaf\Container;
use Seaf\Base;

class Seaf
{
    use Base\SingletonTrait;
    use Component\ComponentCompositeTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct( )
    {
        $this->addComponentLoader(
            new Component\Loader\InitMethodLoader(
                $this
            )
        );

        $this->addComponentLoader(
            new Component\Loader\NamespaceLoader(
                'Seaf\Core\Component'
            )
        );
    }

    public function initRegistry ( )
    {
        return new Container\ArrayContainer();
    }

    public function __callStatic($name, $params)
    {
        $component = static::getSingleton( )->getComponent($name);
        return $component;
    }
}
