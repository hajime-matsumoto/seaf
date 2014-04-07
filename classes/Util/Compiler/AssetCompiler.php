<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\Compiler;

use Seaf\Util\FileSystem\FileLoader;
use Seaf\Exception;
use Seaf;

class AssetCompiler
{
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
        foreach ($this->fileLoader->paths as $path) {
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
        $ext = Seaf::FileSystem($path)->getExt($file);
        // 検索対処
        $file = $this->fileLoader->find($file, $this->map[$ext]['exts']);
        if (!$file) {
            throw new Exception\Exception([
                '%sは存在しません', $path
            ]);
        }


        $real_ext = $file->ext();
        $compiler = $this->$real_ext();
        if(Seaf::Cache()->has((string) $file, $file->mtime()) ) {
            echo Seaf::Cache()->getCachedData((string) $file);
        }else{
            ob_start();
            $compiler->compile($file, $error);
            if (empty($error)) {
                Seaf::Cache()->put((string) $file, 0, $data = ob_get_contents());
                ob_end_clean();
                echo $data;
            }else{
                echo $error;
            }
        }
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
