<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class Helper
{
    public static function getExt ($path)
    {
         if (false === $p = strrpos($path, '.')) return false;
         return substr($path, $p + 1);
    }

    public static function swapExt ($path, $ext)
    {
         if (false === $p = strrpos($path, '.')) return $path.'.'.$ext;
         return substr($path, 0, $p).'.'.$ext;
    }
}
