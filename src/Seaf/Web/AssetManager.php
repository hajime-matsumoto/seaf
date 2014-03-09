<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\FrameWork;
use Seaf\Commander\Command;
use Seaf\Helper\ArrayHelper;

/**
 * Application
 */
class AssetManager extends Application
{
    private $assets_path_list = array();
    private $ext_map = array(
        'css' => array(
            'suffix'=> array('sass','scss','css')
        ),
        'js' => array(
            'suffix'=> array('coffee','js')
        )
    );



    /**
     * 検索パスを追加
     */
    public function addPath($path)
    {
        $this->debug($path.'を追加しました。');
        $this->assets_path_list[] = $path;
    }

    public function initApplication ( )
    {
        parent::initApplication( );

        $this->register('sass', 'Seaf\\Compiler\\Sass',array(),function($sass) {
            foreach($this->assets_path_list as $path) {
                $sass->setOpt("-I", $path);
            }
            return $sass;
        });
        $this->register('coffee', 'Seaf\\Compiler\\Coffee');
    }

    public function run ( )
    {
        $req = $this->request();
        $res = $this->response();

        if ($this->getFile($req->getUri(), $path, $suffix)) {
            switch ($suffix) {
            case 'sass':
                $res->header('Content-Type: text/css;')->sendHeaders();
                $this->sass()->compile($path);
                $this->system()->halt();
                break;
            case 'coffee':
                $res->header('Content-Type: text/javascript;')->sendHeaders();
                $this->coffee()->compile($path);
                $this->system()->halt();
                break;
            }
            $res->header('Content-Type: text/text');
            echo file_get_contents($path);
        }else{
            $this->notfound($req->getUri());
        }
    }

    public function notfound($uri) {
        $this->response()->status(404)->write('Not Found'."<br/>".$uri)->send();
    }

    /**
     * ファイルを取得する
     */
    public function getFile ($file, &$out_path, &$out_suffix)
    {
        $ext = substr($file,strrpos($file,'.')+1);
        $fileName = substr($file,0,strrpos($file,'.'));
        $map = $this->ext_map[$ext];
        //
        // ファイルを探す
        //
        foreach ($this->assets_path_list as $path) {

            foreach ($map['suffix'] as $suffix) {
                $file = $path.'/'.ltrim($fileName,'/').'.'.$suffix;
                if (file_exists($file)) {
                    $out_path = $file;
                    $out_suffix = $suffix;
                    return true;
                }
            }
        }
        return false;
    }
}
