<?php
namespace Seaf\Application\AssetManager;

use Seaf\Application\Base;
use Seaf\Misc\Compiler\Compiler;

/**
 * アセットを管理するクラス
 */
class Application extends Base
{
    public static $ext_map = array(
        'css' => array(
            'sass','scss','less','css'
        ),
        'js' => array(
            'coffee','type','js'
        )
    );

    public function initApplication ( )
    {
        $this->logger()->importHelper($this);
    }

    public function _beforeDispatchLoop($req, $res,$app)
    {
    }

    public function _afterDispatchLoop($req, $res, $app)
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
            $this->kernel()->system()->halt();
        }
    }


    private function findFile($uri, &$out_path,  &$out_ext) 
    {
        $ext = substr($uri,strrpos($uri,'.')+1);

        if (!isset(self::$ext_map[$ext])) {
            $this->warning(array(
                "%sは定義されていない拡張子です",
                $ext
            ));
        }

        $fs = $this->kernel()->fileSystem();
        $cfg = $this->config();
        if (!$cfg->dirs->assets) {
            $this->warning(array(
                "アセットディレクトリdirs.assetsが設定されていません"
            ));
        }

        $find_path = $cfg->dirs->assets.
            substr($uri,0,strrpos($uri,'.')).
            '.{'.implode(',',self::$ext_map[$ext]).'}';
        $files =$fs->glob($find_path, GLOB_BRACE);
        if (empty($files)) {
            $this->warning(array(
                "アセットファイルがみつかりませんでした。in (%s)",
                $find_path
            ));
            return false;
        }
        $out_ext = substr($files[0], strrpos($files[0],'.')+1);
        $out_path = $files[0];

        return true;
    }
}

