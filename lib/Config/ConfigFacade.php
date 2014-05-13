<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * コンフィグモジュール
 */
namespace Seaf\Config;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;
use Seaf\Base\Component;

/**
 * モジュールファサード
 */
class ConfigFacade implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;
    use ConfigureTrait;

    protected static $object_name = 'Config';

    private $prefix;
    private $config_dir;
    private $config_datas;
    private $config_containers = [];
    private $current_section = 'development';

    const DEFAULT_SECTION = 'default';

    public function __construct (Module\ModuleIF $module = null, $config = [])
    {
        if ($module) $this->setParentModule($module);
    }

    protected function proxyRequestGet($req, $name)
    {
        $newReq = clone $req;
        $newReq->addParam('section', $name);
        return $newReq;
    }

    protected function selectProxyHandler($req, $name)
    {
        if ($req->isEmptyParam('section')) {
            return $this;
        }

        $prefix = Util::SeparatedString('.', [
            $this->prefix,$req->getParam('section')
        ]);

        $handler = clone $this;
        $handler->prefix = (string) $prefix;

        return $handler;
    }

    /**
     * キャッシュキーのプレフィックス
     */
    protected function prefix($prefix)
    {
        return $this->prefix.".".$prefix;
    }


    protected function loadConfigDir ($dir)
    {
        $this->debug(['Load Config %s', $dir]);

        $this->config_datas = $this->rootParent( )->cache->config->useCache(
            $dir, function ( ) use ($dir) {
                return $this->retriveAllConfig($dir);
            }, 0, ($this->rootParent()->registry->isDebug() ? time()-10: 0)
        );
    }

    protected function getConfig($name, $default = null)
    {
        $name = $this->prefix($name);
        $cur = $this->getContainer($this->current_section);
        $def = $this->getContainer(self::DEFAULT_SECTION);
        if ($cur->has($name)) {
            return $cur->get($name);
        } elseif($def->has($name)) {
            return $def->get($name);
        }
        return $default;
    }

    protected function getConfigDict($name, $default =[])
    {
        return Util::Dictionary($this->getConfig($name));
    }

    protected function getContainer($name)
    {
        if (!isset($this->config_containers[$name])) {
            $this->config_containers[$name] = new ConfigContainer(
                isset($this->config_datas[$name]) ?  $this->config_datas[$name]: []
            );
        }
        return $this->config_containers[$name];
    }


    private function retriveAllConfig($dir)
    {
        $dir = Util::FileName($dir);

        // キャッシュツリーを構成する
        $configs = [];
        if (!$dir->isDir()) {
            $success = false;
            return [];
        }

        foreach ($dir->glob('*.yaml') as $yaml) {
            $parsed_data = yaml_parse_file($yaml);
            $key = basename($yaml);
            $key = substr($key, 0, strrpos($key,'.'));

            $useSection = isset($parsed_data['useSection']) && $parsed_data['useSection'];
            unset($parsed_data['useSection']);

            if ($useSection) {
                foreach ($parsed_data as $section=>$section_data) {
                    $configs[$section][$key] = $section_data;
                }
            }else{
                $configs[self::DEFAULT_SECTION][$key] = $parsed_data;
            }
        }
        $success = true;
        return $this->filter($configs);
    }

    /**
     * コンフィグ用のフィルタ
     */
    private function filter($datas)
    {
        if (is_array($datas)) {
            foreach ($datas as $k=>$v) {
                $datas[$k] = $this->filter($v);
            }
            return $datas;
        }

        if (is_string($datas)) {
            $datas = preg_replace_callback('/\$([^\$]+)\$/', function($m) {
                return defined($m[1]) ? constant($m[1]): $m[1];
            }, $datas);
            return $datas;
        }

        return $datas;
    }

    protected function dump ( )
    {
        Util::Dump($this->config_datas);
    }
}
