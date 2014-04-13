<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class Dir extends File
{
    public function find ($file)
    {
        $dir = $this->getFileName();
        return new File($file, $dir);
    }
}
