<?php
namespace Seaf\Misc\AssetManager;

use Seaf\Web\Application as WebApplication;
use Seaf\Core\Kernel;

/**
 * アセット
 */
class Application extends WebApplication
{
    /**
     * パスのリスト
     * @var array;
     */
    private $assets_path_list = array('/assets');

    /**
     * 拡張子と検索する拡張子のリスト
     * @var array
     */
    private $ext_map = array(
        'css' => array(
            'suffix'=> array('sass','scss','css')
        ),
        'js' => array(
            'suffix'=> array('coffee','js')
        )
    );

    public function initApplication ( )
    {
        parent::initApplication();

        // イベントをオールクリアする
        $this->event()->clear();

        /**
         * SASSコンパイルのオプションにassets_path_listをインクルード
         * 対象に入れる処理を追加する
         */
        $this->register('sass', 'Seaf\\Misc\\Compiler\\Sass',array(),function($sass) {
            foreach($this->assets_path_list as $path) {
                $sass->setOpt("-I", $this->fs()->transRealPath($path));
            }
            return $sass;
        });

        // Coffeeスクリプトのコンパイラ
        $this->register('coffee', 'Seaf\\Misc\\Compiler\\Coffee');
    }

    /**
     * 検索パスを追加
     */
    public function addPath($path)
    {
        $this->assets_path_list[] = $path;
        return $this;
    }

    /**
     * @SeafRoute *
     */
    public function catchAll($req, $res, $app)
    {
        $uri = $req->uri();

        // ファイルを探す
        if ($this->getFile($uri, $path, $suffix)) {

            // 拡張子別の処理
            switch ($suffix) {
            case 'sass':
                $res
                    ->status(200)
                    ->cache(60 * 60 * 24)
                    ->header('Content-Type', 'text/css')
                    ->sendHeaders();
                $this->sass()->compile($path);
                $this->sys()->halt();
                return true;
                break;
            case 'coffee':
                $res
                    ->status(200)
                    ->cache(60 * 60 * 24)
                    ->header('Content-Type', 'text/javascript')
                    ->sendHeaders();
                $this->coffee()->compile($path);
                $this->sys()->halt();
                return true;
                break;
            }

            // 登録されてない拡張子の場合
            echo file_get_contents($path);
            return true;
        } 
        return false;
    }

    public function _notfound($body = null, $code = '404')
    {
        return false;
        $this->response()
            ->status(404)
            ->write('<h1>404 Not Found</h1>')
            ->write($body)
            ->send();
    }

    /**
     * ファイルを取得する
     *
     * @param string 検索するファイル名
     * @param string 見つかったファイル名
     * @param string 見つかったファイルの拡張子
     * @return bool ファイルが見つかっていなければFALSE
     */
    private function getFile ($file, &$out_path, &$out_suffix)
    {
        $ext      = substr($file,strrpos($file,'.')+1);
        $fileName = substr($file,0,strrpos($file,'.'));
        if (isset($this->ext_map[$ext])) {
            $map = $this->ext_map[$ext];
        } else {
            $map = array('suffic' => array($ext));
        }
        //
        // ファイルを探す
        //
        foreach ($this->assets_path_list as $path) {
            foreach ($map['suffix'] as $suffix) {
                $file = $path.'/'.ltrim($fileName,'/').'.'.$suffix;
                if ($this->fs()->fileExists($file)) {
                    $out_path = $file;
                    $out_suffix = $suffix;
                    return true;
                }
            }
        }
        return false;
    }
}
