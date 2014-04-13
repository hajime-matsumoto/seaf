<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf\Exception;

class ModuleLoader
{
    private $paths;

    public function addPath ($path)
    {
        $this->paths[] = $path;
    }

    public function enable ($name)
    {
        foreach ($this->paths as $path)
        {
            $file = $path.'/'.$name.'/__module.php';
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }

        throw new Exception\Exception([
            "%sモジュールが見つかりません",
            $name
        ]);
    }
}
