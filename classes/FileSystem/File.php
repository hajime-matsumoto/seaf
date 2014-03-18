<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\FileSystem;

use Seaf\Exception;

class File
{
    protected $name;

    public function __construct ($name)
    {
        $this->name = $name;
        $this->parsers = array(
            'yaml' => array($this, 'parseYaml'),
            'php' => array($this, 'parsePHP')
        );
    }

    public function isDir( )
    {
        return false;
    }

    public function exists ( )
    {
        return Helper::exists($this->name);
    }

    public function ext ( )
    {
        return substr($this->name, strrpos($this->name, '.') + 1);
    }

    public function parseToArray ( )
    {
        if (!$this->exists()) {
            throw new Exception\Exception(array(
                "%sは存在しないファイルです。",
                $this->name
            ));
        }
        $parser = $this->parsers[$this->ext()];

        return $parser($this->name);
    }

    public function parseYaml ($file)
    {
        return yaml_parse_file($file);
    }

    public function parsePHP ($file)
    {
        return include $file;
    }
}
