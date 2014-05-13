<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * システムモジュール
 */
namespace Seaf\System;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;
use Seaf\Base\Module;

/**
 * モジュールファサード
 */
class SuperGlobalsFacade implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;

    protected static $object_name = 'super_globals';

    private $container;

    public function __construct(Module\ModuleIF $module  = null)
    {
        if ($module) {
            $this->setParentModule($module);
        }

        $argc = isset($GLOBALS['argc']) ? $GLOBALS['argc']: 0;
        $argv = isset($GLOBALS['argv']) ? $GLOBALS['argv']: [];

        $this->container = Util::Dictionary([
            '_SERVER'  => $_SERVER,
            '_GET'     => $_GET,
            '_POST'    => $_POST,
            '_FILES'   => $_FILES,
            '_REQUEST' => $_REQUEST,
            '_ENV'     => $_ENV,
            '_COOKIE'  => $_COOKIE,
            'argc'     => $argc,
            'argv'     => $argv
        ]);
        $this->container->useDotedName(true);
    }

    /**
     * スーパーグローバルズからデータを取得する
     *
     * @param string $name
     * @param mixed $default = null
     * @return mixed
     */
    public function get ($name, $default = null)
    {
        return $this->container->get($name, $default);
    }

    /**
     * データをセットする
     *
     * @param string|array $name
     * @param mixed $value = null
     * @return self
     */
    public function set ($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) {
                $this->set($k, $v);
            }
            return $this;
        }

        $this->debug(["Set >>> $name = %s <<<", is_string($value) ? $value: gettype($value)]);
        $this->container->set($name, $value);
        return $this;
    }

    /**
     * ディクショナリ型で取得する
     */
    public function dict ($name)
    {
        return $this->container->dict($name);
    }


    /**
     * コンテナを取得する
     *
     * @return Seaf\Base\Types\Dictionary
     */
    public function container ( )
    {
        return $this->container;
    }

    public function dump( )
    {
        Util::Dump($this->container);
    }
}
