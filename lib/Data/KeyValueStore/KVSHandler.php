<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

use Seaf\Container;
use Seaf\Factory;
use Seaf\Component;
use Seaf\Base;
use Seaf\Event;

class KVSHandler
{
    private $default = 'FileSystem';

    use Base\SingletonTrait;
    use Base\RaiseErrorTrait;
    use Component\ComponentCompositeTrait;
    use Event\ObservableTrait;

    /**
     * クラス名の遅延束縛
     */
    public static function who ( )
    {
        return __CLASS__;
    }

    public static function factory ($cfg)
    {
        $cfg = Container\ArrayHelper::useContainer($cfg);
        $kvs = new static( );
        $kvs->default = $cfg('default', 'FileSystem');
        $kvs->loadComponentConfig($cfg('component'));
        return $kvs;
    }

    public function __construct ( )
    {
        $this->addComponentLoader(
            new Component\Loader\NamespaceLoader(__NAMESPACE__.'\\Component')
        );
    }

    /**
     * 処理用のハンドラを取得する
     */
    public function table($name = 'default', $engine = null)
    {
        if ($engine == null) $engine = $this->default;

        return new Table($name, $this->getComponent($engine));
    }

}
