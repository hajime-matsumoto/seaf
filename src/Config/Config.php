<?php

namespace Seaf\Config;
use Seaf;
use Seaf\Kernel\Kernel;

class Config
{
    const DEFAULT_SECTION = 'default';

    private $container;

    private $section = 'development';
    private $sections = array();

    public function __construct ($config)
    {
        if (is_string($config) && file_exists($config)) {
            $this->load($config);
        }
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __invoke ($name = null, $default = null)
    {
        if ($name == null) return $this;
        return $this->get($name, $default);
    }

    public function load ($file)
    {
        $ext = substr($file, strrpos($file,'.')+1);
        switch ($ext) {
        case 'php':
            break;
        case 'yaml':
            $this->loadArray(Kernel::fileSystem()->loadYaml($file));
            break;
        default:
            Seaf::warn(array("%sは対応していない形式の様です",$file));
            break;
        }
    }

    public function loadArray($data)
    {
        foreach($data as $k => $v) {
            $this->sections[$k] = new Container($v, $this);
        }
    }

    public function get ($name, $default = null)
    {
        $current = $this->sections[$this->section];
        if ($current->has($name)){
            return $current->get($name);
        } else {
            return $this->sections[self::DEFAULT_SECTION]->get($name, $default);
        }
    }

    public function toArray()
    {
        $c = isset($this->section[$this->section]) ?  $this->section[$this->section]: null;
        return array_merge_recursive(
            $c instanceof self ? $c->toArray(): array(),
            $this->sections[self::DEFAULT_SECTION]->toArray()
        );
    }
}

class Container extends \Seaf\Core\Container\Container
{
    private $config;

    public function __construct($data, $config)
    {
        $this->config = $config;
        parent::__construct($data);
    }

    public static function factory ($data, $config)
    {
        if (empty($data)) return null;
        if (is_array($data)) return new Container($data, $config);
        return $data;
    }

    public function set($name, $value = null)
    {
        parent::set($name, self::factory($value, $this->config));
    }

    public function get($name, $value = null)
    {
        if (false !== strpos($name, '.'))
        {
            $token = strtok($name,'.');
            $head = $this;
            do {
                if (!$head->has($token)) {
                    return false;
                }
                $head = $head->get($token);
            } while($token = strtok('.'));

            return $head;
        }

        $data = parent::get($name,$value);
        return $this->outputFilter($data);
    }

    public function outputFilter($data) 
    {
        $config = $this->config;
        if (is_string($data)) {
            return preg_replace_callback('/\$(.+)\$/', function ($m) use ($config) {
                return defined($m[1]) ? constant($m[1]) : $config->get($m[1],$m[1]);
            }, $data);
        }
        return $data;
    }

    public function toArray()
    {
        $ret = array();
        foreach ($this->data as $k => $v)
        {
            $ret[$k] = $v instanceof self ? $v->toArray(): $this->get($k);
        }
        return $ret;
    }

}

