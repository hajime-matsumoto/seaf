<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\FileSystem;

class Helper
{
    public static function directory ($path)
    {
        return new Directory($path);
    }

    public static function file ($path)
    {
        return new file($path);
    }

    public static function isDir ($path)
    {
        return is_dir($path);
    }

    public static function exists ($path)
    {
        return file_exists($path);
    }
}
