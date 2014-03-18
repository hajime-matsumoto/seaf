<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Kernel\Component;

use Seaf\FileSystem as FS;
use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;

/**
 * モジュールローダ
 *
 * 使い方
 *
 * - addModulePath([path]);
 * - load([name])
 */
class ModuleLoader
{
    private $paths = array();

    /**
     * コンストラクタ
     */
    public function __construct ()
    {
        // Seafのデフォルトモジュールパスを設定
        if (defined('SEAF_MODULES_PATH')) {
            $this->addModulePath(SEAF_MODULES_PATH);
        }
    }

    /**
     * モジュールパスを追加
     *
     * @param string $path
     * @param bool $append
     */
    public function addModulePath ($path, $append = true)
    {
        if (!FS\Helper::isDir($path)) {
            throw new Exception(array(
                "%sはディレクトリではありません",
                $path
            ));
        }

        if ($append) {
            $this->paths[] = $path;
        } else {
            array_unshift($this->paths, $path);
        }
    }

    /**
     * モジュールをロードする
     *
     * @param string $name
     * @param bool $append
     */
    public function load ($name)
    {
        foreach ($this->paths as $path) {
            Kernel::Event()->trigger('module.'.$name.'.loaded');
            FS\Helper::directory($path)->includeOnce($name.'/__autoload.php');
            return true;
        }
        throw new Exception(array(
            "%sはロードできませんでした",
            $name
        ));
    }
}
