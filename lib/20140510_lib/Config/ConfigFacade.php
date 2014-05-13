<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Config;

use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Util\Util;

/**
 * 
 */
class ConfigFacade extends Module\Facade
{
    const DEFAULT_SECTION = 'default';

    private $config_dir;
    private $config_datas;
    private $mediator;
    private $config_containers = [];
    private $current_section = 'development';

    public function __construct ($config = [])
    {
        $c = Util::ArrayContainer($config, [
            'dir' => __DIR__
        ]);

        $this->config_dir = $c('dir');
    }

    public function dump ( )
    {
        Util::dump($this->config_datas);
    }

    public function initWithMediator($mediator)
    {
        $this->mediator = $mediator;
        $dir = Util::FileName($this->config_dir);

        // キャッシュ経由でデータを取得
        $this->config_datas = $this->mediator->cache->config->useCache(
            'config_all',
            function (&$success) use ($dir) {
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

                    $useSection = isset($parsed_data['useSection']) && $parsed_data['useSection'] ?
                        true:
                        false;
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
            },0,($this->mediator->registry->isDebug() ? time(): 0)
        );
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
            return $datas;
        }

        return $datas;
    }


    /**
     * @See FacadeIF
     */
    public function execute (Command\RequestIF $request, $from = null)
    {
        $targets = $request->dict('target')->toArray();
        return parent::execute($request, $from);
    }

    public function getConfig($name, $default = null)
    {
        $cur = $this->getContainer($this->current_section);
        $def = $this->getContainer(self::DEFAULT_SECTION);
        if ($cur->has($name)) {
            return $cur->get($name);
        } elseif($def->has($name)) {
            return $def->get($name);
        }
        return $default;
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

}
