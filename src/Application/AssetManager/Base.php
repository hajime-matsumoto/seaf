<?php
namespace Seaf\Application\AssetManager;

use Seaf\Application\Base as ApplicationBase;
use Seaf\Kernel\Kernel;
use Seaf\Misc\Compiler\Compiler;

/**
 * アセットを管理するクラス
 */
class Base extends ApplicationBase
{
    public static $ext_map = array(
        'css' => array(
            'sass','scss','less','css'
        ),
        'js' => array(
            'coffee','type','js'
        )
    );

    /**
     * @var FileSystemFile
     */
    private $dir;

    public function initApplication ( )
    {
        $this->logger()->importHelper( );

        // Config Helper
        $cfg = $this->config()->getHelper();
        
        if (!$cfg->has('dirs.assets')) {
            $this->warning(array(
                "アセットディレクトリdirs.assetsが設定されていません"
            ));
        }

        $dir = Kernel::fileSystem($cfg('dirs.assets'));

        if(!$dir->isDir()) {
            $this->warning(array(
                "%sはディレクトリではありません",
                (string) $dir
            ));
        }
        $this->dir = $dir;
    }

    public function _beforeDispatchLoop($req, $res,$app)
    {
    }

    public function _afterDispatchLoop($req, $res, $app, $isDispatched)
    {
        $uri = $req->uri;

        if ($this->findFile($uri, $path, $ext)) {

            // コンパイラを取得
            $compiler = Compiler::factory($ext);

            // 拡張子別の処理
            switch($ext){
            case 'sass':
                $res->header('Content-Type','text/css');
                $compiler->setOpt('-I', $this->config()->dirs->assets);
                $compiler->setOpt('--cache-location', $this->config()->dirs->cache, true);
                break;
            case 'coffee':
                $res->header('Content-Type','text/javascript');
            }
            $res->sendHeaders();
            $compiler->compile($path);
            Kernel::system()->halt();
        }
    }


    private function findFile($uri, &$out_path,  &$out_ext) 
    {
        $ext = substr($uri,strrpos($uri,'.')+1);
        $cfg = $this->config()->getHelper();

        if (!isset(self::$ext_map[$ext])) {
            $this->warning(array(
                "%sは定義されていない拡張子です",
                $ext
            ));
        }

        $path = substr($uri,0,strrpos($uri,'.')).
            '.{'
            .implode(',',self::$ext_map[$ext])
            .'}';
        $files = $this->dir->find($path);
        if (empty($files)) {
            $this->warning(array(
                "アセットファイルがみつかりませんでした。in (%s/%s)",
                $this->dir,
                $path
            ));
            return false;
        }
        $out_ext = $files[0]->ext();
        $out_path = (string) $files[0];

        return true;
    }
}

