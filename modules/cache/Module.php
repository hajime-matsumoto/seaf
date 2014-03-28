<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Cache;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * Cache モジュール
 */
class Module
{
    /**
     * Cacheエンジン
     *
     * @var Engine\Base
     */
    private $engine;

    /**
     * Cacheモジュールを作成する
     *
     * @param array $config
     * @return Module
     */
    public static function factory ($config = array())
    {
        $get = ArrayHelper::getter();

        $module = new Module();

        $engine = $get($config, 'engine', array(
            'type' => 'file',
            'config' => array(
                'path' => '/tmp/cache'
            )
        ));

        $module->setEngine($engine);
        return $module;
    }

    /**
     * ハンドラをセットする
     *
     * @param array
     */
    public function setEngine ($config)
    {
        $get = ArrayHelper::getter();
        $type = $get($config, 'type');

        return $this->engine = Seaf::ReflectionClass(
            sprintf(
                '%s\\Engine\\%sEngine',
                __NAMESPACE__,
                ucfirst($type)
            )
        )->newInstance($get($config, 'config'));
    }

    public function flush ( )
    {
        $this->engine->flush();
    }

    public function has ($key)
    {
        return $this->engine->has($key);
    }

    public function set ($key, $value, $expire = 0)
    {
        return $this->engine->set($key, $value, $expire);
    }

    public function get ($key, &$stat = null)
    {
        return $this->engine->get($key, $stat);
    }
}
