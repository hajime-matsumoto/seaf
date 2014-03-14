<?php
/**
 * データ操作:コンフィグ
 */
namespace Seaf\Data\Config;

use Seaf\Kernel\Kernel;

/**
 * 配列マージ関数をオーバライドする
 */
if (!function_exists('array_merge')) {
    function array_merge($a1, $a2) {
        $newArray = array();
        $keys = \array_merge(array_keys($a1),array_keys($a2));
        array_unique($keys);
        foreach ($keys as $k) {
            if (isset($a1[$k]) && !isset($a2[$k])) {
                $newArray[$k] = $a1[$k];
                continue;
            }
            if (!isset($a1[$k]) && isset($a2[$k])) {
                $newArray[$k] = $a2[$k];
                continue;
            }
            if (is_array($a1[$k]) && is_array($a2[$k])) {
                $newArray[$k] = array_merge($a1[$k], $a2[$k]);
                continue;
            }
            $newArray[$k] = $a2[$k];
        }
        return $newArray;
    }
}

/**
 * コンフィグクラス
 */
class Config
{
    const DEFAULT_SECTION = 'default';

    private $section = 'development';

    private $sections = array();

    public function __invoke ($config = false)
    {
        if ($config == false) return $this;

        return $this->get($config);
    }

    public function load ($file)
    {
        $file = Kernel::fileSystem($file);
        $this->loadArray($file->parse());
        return $this;
    }

    public function loadArray ($array)
    {
        foreach($array as $section => $vars) {
            if (empty($vars)) $vars = array();
            $sections[$section] = $vars;
        }

        foreach($sections as $k=>$section)
        {
            if ($k==self::DEFAULT_SECTION) continue;
            $sections[$k] = array_merge(
                $sections[self::DEFAULT_SECTION],
                $sections[$k]
            );
            $this->sections[$k] = new Container($sections[$k], $this);
        }
    }

    public function get ($name, $default = false)
    {
        if (!$this->sections[$this->section]->has($name)) {
            return $default;
        }
        return $this->sections[$this->section]->get($name);
    }

    public function has ($name)
    {
        if ($this->sections[$this->section]->has($name)) {
            return true;
        }
        return $this->sections[$this->section]->has($name);
    }


    public function __get($name)
    {
        return $this->sections[$this->section]->get($name);
    }

    public function getHelper ( )
    {
        return new Helper($this);
    }
}

class Helper
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function __invoke ($name)
    {
        return $this->config->get($name);
    }

    public function has($name)
    {
        return $this->config->has($name);
    }

    public function getString($name)
    {
        if ($this->config->has($name)) {
            return $this->config->get($name)->toString();
        }
        return null;
    }

    public function getArray($name)
    {
        if ($this->config->has($name)) {
            return $this->config->get($name)->toArray();
        }
        return array();
    }
}


