<?php

namespace Seaf\Config;
use Seaf\Helper\ArrayHelper;

class Config
{
    const DEFAULT_SECTION = 'default';
    private $section;

    public function __construct ($section = self::DEFAULT_SECTION)
    {
        $this->section = $section;
    }

    public function load ($file)
    {
        if (!file_exists($file)) {
            throw \Exception($file." は存在しません");
        }
        $ext = substr($file,strrpos($file,'.')+1);

        switch ($ext) {
        case 'php':
            $array = include $file;
            break;
        case 'yaml':
            $array = yaml_parse_file($file);
            break;
        default:
            throw \Exception($ext." には対応していません");
        }

        $this->configs = $array;
    }

    public function get($name, $value = null)
    {
        if (is_array($name)) {
            $ret = array();
            foreach($name as $v) {
                $ret[$v] = $this->get($v);
            }
            return $ret;
        }
        $ret = ArrayHelper::getWithDot(
            $this->configs[$this->section],
            $name,
            ArrayHelper::getWithDot(
                $this->configs[self::DEFAULT_SECTION],
                $name,
                $value
            )
        );
        return $ret;
    }

    public function set($name, $value = null)
    {
        ArrayHelper::setWithDot($this->configs[$this->section], $name, $value);
        return $this;
    }
}
