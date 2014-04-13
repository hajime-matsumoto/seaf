<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\Compiler;

use Seaf\Util\FileSystem\Loader as FileLoader;
use Seaf\Util\FileSystem;
use Seaf\Exception;
use Seaf;
use Seaf\Base;

class AssetCompiler
{
    use Base\SeafAccessTrait;
    use Base\CacheTrait;

    public $fileLoader;
    private $compilers;
    private $map = [
        'js' => [
            'mime'=>'text/javascript',
            'exts'=>['js','coffee']
        ],
        'css' => [
            'mime'=>'text/stylesheet',
            'exts'=>['css','sass']
        ]
    ];



    public function __construct( )
    {
        $this->fileLoader = new FileLoader();
    }

    /**
     * SASSコンパイラのイニシャライザ
     */
    private function _sass ( )
    {
        $sass = new Command\Sass( );
        foreach ($this->fileLoader->getPaths() as $path) {
            $sass->setOpt('-I',$path);
        }
        return $sass;
    }

    /**
     * COFFEEコンパイラのイニシャライザ
     */
    private function _coffee ( )
    {
        $coffee = new Command\Coffee( );
        return $coffee;
    }

    /**
     * コンパイルする
     */
    public function compile ($path)
    {
        // 拡張子判定
        $ext = FileSystem\Helper::getExt($path);

        // 検索対処
        foreach ($this->map[$ext]['exts'] as $try_ext) {
            $file = $this->fileLoader->file(FileSystem\Helper::swapExt($path, $try_ext));
            if ($file) {
                break;
            }
        }

        if (!$file) {
            throw new Exception\Exception([
                '%sは存在しません', $path
            ]);
        }

        echo $this->useCache((string) $file, function (&$isSuccess) use ($file) {
            $real_ext = $file->ext();
            $compiler = $this->$real_ext();

            ob_start();
            $compiler->compile($file, $error);
            if (!empty($error)) {
                $isSuccess = false;
                return $error.ob_get_clean();
            }else{
                return ob_get_clean();
            }
        }, 0, $file->mtime());
    }

    public function __call($name, $params)
    {
        if (method_exists($this, $method = "_".$name)) {
            if( isset($this->compilers[$name])) {
                return $this->compilers[$name];
            }else{
                return $this->compilers[$name] = call_user_func([$this,$method]);
            }
        }
        throw new Exception\Exception([
            "%s->%s は定義されていません", get_class($this), $name
        ]);
    }
}
