<?php
namespace Seaf\Kernel\Module;

use Seaf\Pattern\ExtendableMethods;
use Seaf\Exception\Exception;

class FileSystem
{
    public function __construct ( )
    {
    }

    public function loadYaml ($file)
    {
        return yaml_parse_file($file);
    }

    public function mkdir ($dir, $perm)
    {
        if (!is_dir($dir)) mkdir($dir, $perm, $recursive = true);
        return $this;
    }

    public function glob ($pattern, $switch = GLOB_BRACE)
    {
        return glob($pattern, $switch);
    }

    public function getContents($file)
    {
        return file_get_contents($file);
    }

    public function __invoke ($file = null)
    {
        if ($file == null) return $this;
        return new FileSystemFile($file);
    }
}

class FileSystemFile
{
    private $file = "";
    private $ext = "";

    public function __construct($file)
    {
        $this->file = $file;
        $this->ext = substr($file, strrpos($file,'.')+1);
    }

    public function toString()
    {
        return (string) $this->file;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function basename($no_ext = false)
    {
        if ($no_ext == false) return basename($this->file);

        $basename = basename($this->file);
        return substr($basename, 0, strrpos($basename, '.'));
    }

    public function exists ( )
    {
        return file_exists($this->file);
    }

    public function find ($pattern)
    {
        $files = glob($this->file.'/'.$pattern);
        foreach ($files as $k=>$file) {
            $files[$k] = new self($file);
        }
        return $files;
    }

    public function doRequire ($once = true)
    {
        if ($once) {
            require_once $this->file;
        } else {
            require $this->file;
        }
    }

    public function parse()
    {
        if (!file_exists($this->file)) {
            throw new Exception(array(
                "%sは存在しないファイルです",
                $this->file));
        }
        switch ($this->ext) {
        case 'yaml':
            return yaml_parse_file($this->file);
            break;
        case 'php':
            return include $this->file;
            break;
        }

        throw new Exception(array(
            "%sはparseできないファイルです",
            $this->ext));
    }
}
