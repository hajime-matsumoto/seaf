<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Core;
use Seaf\Cache;
use Seaf\Data\KeyValueStore as KVS;
use Seaf\Container;

/**
 * コンフィグコンポーネント
 */
class Config implements Core\ComponentIF
{
    private $cfg;

    private $cache;

    private $sections;

    private $defaultSectionName = 'default';

    public function __construct ($cfg)
    {
        $this->cfg = $cfg;
    }

    public function initSeafComponent(Seaf $seaf)
    {
        $this->env = $seaf->Registry( )->getVar('env');

        $this->cache = Cache\CacheHandler::getSingleton( )->section('config');
    }

    public function loadConfigFiles ($dir)
    {
        $Dir = dir($dir);
        while ($file = $Dir->read()) {
            if ($file{0} == '.') continue;
            $this->loadConfigFile (
                substr($file, 0, strpos($file,'.')),
                $dir.'/'.$file
            );
        }
        return $this;
    }

    public function loadConfigFile ($key, $file)
    {
        $data = $this->cache->useCache($file, function (&$isSuccess = null) use ($file) {
            $data = yaml_parse_file($file);
            if (is_array($data)) {
                $isSuccess = true;
            }
            return $data;
        }, 0, filemtime($file), $cacheStatus);

        if (isset($data['useSection']) && $data['useSection'] == 1) {
            unset($data['useSection']);
            foreach ($data as $section=>$vars) {
                $this->getConfigContainer($section)->setVar($key, $vars);
            }
        }else{
            unset($data['useSection']);
            $this->getDefaultSection()->setVar($key, $data);
        }
    }

    public function getConfig($key, $default = null)
    {
        $hasValue = false;

        if($this->getCurrentSection( )->hasVar($key)) {
            $value = $this->getCurrentSection()->getVar($key);
            $hasValue = true;
        }elseif($this->getDefaultSection( )->hasVar($key)) {
            $value = $this->getDefaultSection()->getVar($key);
            $hasValue = true;
        }
        return $hasValue ? $this->configFilter($value): $default;
    }

    public function __invoke($key, $default)
    {
        return $this->getConfig($key, $default);
    }


    protected function getConfigContainer($section)
    {
        if (!isset($this->sections[$section])) {
            $this->sections[$section] = new Container\ArrayContainer( );
        }
        return $this->sections[$section];
    }

    protected function getCurrentSection( ) 
    {
        return $this->getConfigContainer($this->env);
    }

    protected function getDefaultSection( ) 
    {
        return $this->getConfigContainer($this->defaultSectionName);
    }

    protected function configFilter($value)
    {
        if (is_array($value)) {
            foreach ($value as $k=>$v) {
                $value[$k] = $this->configFilter($v);
            }
        }else{
            $value = preg_replace_callback('/\$([^\$]+)\$/', function($m) {
                if(defined($m[1])) return constant($m[1]);
                return $this->getConfig($m[1], $m[1]);
            }, $value);
        }
        return $value;
    }
}
