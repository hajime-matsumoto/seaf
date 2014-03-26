<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class Base
{
    public function mkdir ($path, $mode = 01777)
    {
        if (!is_dir($path)) {
            mkdir($path, $mode, true);
        }
        return $this->helper($path);
    }

    public function helper ($path = null)
    {
        if ($path == null) return $this;
        return new File($path);
    }
}
