<?php
namespace Seaf\Kernel\Module;

use Seaf\Core\Pattern\ExtendableMethods;

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

}
