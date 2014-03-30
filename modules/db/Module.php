<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Module\DB;

use Seaf;
use Seaf\Util\ArrayHelper;

/**
 * DBモジュール
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
     * DBモジュールを作成する
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
     * DBハンドラを追加する
     *
     * @param string $name
     * @param array $config
     */
    public function addHandler($name, $config)
    {
        $get = ArrayHelper::getter();
        $handler = Handler::factory($config);
        $this->handlers[$name] = $handler;
        return $handler;
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

    public function __invoke ($name = null)
    {
        if ($name == null) return $this;
        return $this->getHandler($name);
    }

    public function getHandler($name)
    {
        return $this->handlers[$name];
    }

}
