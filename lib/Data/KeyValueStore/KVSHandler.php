<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

use Seaf\Container;
use Seaf\Factory;
use Seaf\Component;
use Seaf\Base;

class KVSHandler
{
    private $default;

    use Base\RaiseErrorTrait;
    use Component\ComponentCompositeTrait;

    public static function factory ($cfg)
    {
        $cfg = Container\ArrayHelper::useContainer($cfg);
        $kvs = new static( );
        $kvs->default = $cfg('default');
        $kvs->loadComponentConfig($cfg('component'));
        return $kvs;
    }

    public function __construct ( )
    {
        $this->setErrorCode('COMPONENT_NOT_FOUND', 'Exception');

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
