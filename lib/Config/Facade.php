<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * コンフィグモジュール
 */
namespace Seaf\Config;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;

/**
 * モジュールファサード
 */
class Facade implements BackEnd\Module\ModuleFacadeIF
{
    use BackEnd\Module\ModuleFacadeTrait {
        BackEnd\Module\ModuleFacadeTrait::__proxyRequestCall as ParentProxyRequestCall;
    }

    const DEFAULT_SECTION = 'default';

    private $cfg;
    private $prefix;
    private $config_dir;
    private $config_datas;
    private $config_containers = [];
    private $current_section = 'development';

    public function __construct ($cfg = [])
    {
        $this->cfg = $cfg;
    }

    public function initFacade( )
    {
        if (!empty($this->cfg)) {
            $this->setup($this->cfg);
        }
    }

    public function setup($cfg) 
    {
        $c = Util::Dictionary($cfg);
        // コンフィグディレクトリを指定
        $this->config_dir = $c->dir;

        $reg = $this->root()->registry;
        $this->config_datas = $this->root()->cache->config->useCache(
            $c->get('dir','all'), function(&$s) {
                return $this->retriveAllConfig();
            }, 0, ($reg->isDebug() ? time()-10: 0)
        );
    }

    public function prefix($name)
    {
        if (!$this->prefix) return $name;
        return $this->prefix.".".$name;
    }

    public function __proxyRequestCall(Proxy\ProxyRequestIF $request, $name, $params)
    {
        if(!$request->hasParam('section')) {
            return $this->ParentProxyRequestCall($request, $name, $params);
        }

        $facade = clone $this;
        $facade->prefix = implode('.', $request->getParam('section',[]));
        $new_request = clone $request;
        $new_request->clearParam('section');

        return $facade->__proxyRequestCall($new_request, $name, $params);
    }


    public function getConfig($name, $default = null)
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
    public function getConfigDict($name, $default =[])
    {
        return Util::Dictionary($this->getConfig($name));
    }

    public function getContainer($name)
    {
        if (!isset($this->config_containers[$name])) {
            $this->config_containers[$name] = new ConfigContainer(
                isset($this->config_datas[$name]) ?  $this->config_datas[$name]: []
            );
        }
        return $this->config_containers[$name];
    }

    private function retriveAllConfig( )
    {
        $dir = Util::FileName($this->config_dir);

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

    public function dump( )
    {
        Util::Dump($this->config_datas);
    }
}
