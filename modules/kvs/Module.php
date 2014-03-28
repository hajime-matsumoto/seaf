<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\Kvs;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * Key Valus Store モジュール
 */
class Module 
{
    const DEFAULT_HANDLER = 'default';

    /**
     * ハンドラリスト
     *
     * @var array
     */
    private $handlers = array();

    /**
     * KVSモジュールを作成する
     *
     * @param array $config
     * @return Module
     */
    public static function factory ($config = array())
    {
        $get = ArrayHelper::getter();

        $module = new Module();

        foreach ($get($config, 'handlers', array()) as $name => $handler)
        {
            $module->addHandler($name, $handler);
        }

        return $module;
    }

    /**
     * KVSハンドラを追加する
     *
     * @param string $name
     * @param array $config
     */
    public function addHandler($name, $config)
    {
        $get = ArrayHelper::getter();
        $type = $get($config, 'type', 'file');

        // ハンドラを作成する
        $class = Seaf::ReflectionClass(
            __NAMESPACE__.'\\Engine\\'.ucfirst($type).'Engine'
        );

        $handler = $class->newInstance($get($config, 'config', array()));
        $this->handlers[$name] = $handler;
        return $handler;
    }

    /**
     * Getは参照を使うので実装
     */
    public function get ($name, &$stat = null)
    {
        return $this->handlers[self::DEFAULT_HANDLER]->get($name, $stat);
    }

    /**
     * 所有していない呼び出しはデフォルトハンドラへの呼び出しとする
     */
    public function __call ($name, $params)
    {
        return call_user_func_array (
            array ($this->handlers[self::DEFAULT_HANDLER], $name),
            $params
        );
    }

}
