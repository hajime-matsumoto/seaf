<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

use Seaf\Component;
use Seaf\Container;
use Seaf\Base;
use Seaf\Cache;
use Seaf\Data\KeyValueStore;

class Seaf
{
    use Base\SingletonTrait;
    use Component\ComponentCompositeTrait;

    public static function who ( )
    {
        return __CLASS__;
    }

    public static function __callStatic($name, $params)
    {
        $component = static::getSingleton( )->getComponent($name);
        return $component;
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

        $this->on('component.create', function ($e) {
            $instance = $e->getVar('component');

            if ($instance instanceof Seaf\Core\ComponentIF) {
                $instance->initSeafComponent($this);
            }
        });
    }

    public function __call($name, $params)
    {
        $component = $this->getComponent($name);
        return $component;
    }

    public function init ($project_root, $env = 'development', $options = [])
    {
        // スタートアップ時の設定をする
        $this->Registry( )
            ->setVar($options)
            ->setVar([
                'project_root' => $project_root,
                'env'          => $env
            ]);

        // 設定を読み込む
        $c = $this->Config( )->loadConfigFiles($project_root.'/configs');

        // PHPを設定する
        mb_internal_encoding($c('setting.encoding', 'utf-8'));
        mb_language($c('setting.lang', 'ja'));
        date_default_timezone_set($c('setting.timezone', 'Asia/Tokyo'));

        $this->loadComponentConfig($c('component', []));
    }


    /**
     * レジストリの作成
     */
    public function initRegistry ( )
    {
        $data = new Container\ArrayContainer();
        return $data;
    }

    /**
     * KVSハンドラの作成
     */
    public function initKeyValueStore($cfg)
    {
        return KeyValueStore\KVSHandler::factory($cfg);
    }

    /**
     * キャッシュハンドラの作成
     */
    public function initCache($cfg)
    {
        $cache = new Cache\CacheHandler($cfg('table', 'cache'));
        $cache->setKvsTable($this->KeyValueStore( )->table(
            $cfg('table', 'cache'),
            $cfg('KeyValueStore', 'FileSystem')
        ));
        return $cache;
    }

}
