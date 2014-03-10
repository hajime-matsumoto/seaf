<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\FrameWork;
use Seaf\Commander\Command;
use Seaf\Helper\ArrayHelper;

/**
 * アセットマネージャー
 * ===================================
 *
 * SASSやCOFFEEなど、コンパイルが必要なアセットを
 * アクセスのタイミングでコンパイルする
 *
 * 使い方
 * -----------------------------------
 * <code>
 * $am = new AssetManager();
 * $am->addPath(<アセットファイルの検索パス>);
 * $am->run();
 * </code>
 *
 */
class AssetManager extends Application
{
    /**
     * パスのリスト
     * @var array;
     */
    private $assets_path_list = array();

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

    /**
     * 初期処理
     */
    public function initApplication ( )
    {
        parent::initApplication( );

        /**
         * SASSコンパイルのオプションにassets_path_listをインクルード
         * 対象に入れる処理を追加する
         */
        $this->register('sass', 'Seaf\\Compiler\\Sass',array(),function($sass) {
            foreach($this->assets_path_list as $path) {
                $sass->setOpt("-I", $path);
            }
            return $sass;
        });

        // Coffeeスクリプトのコンパイラ
        $this->register('coffee', 'Seaf\\Compiler\\Coffee');
    }


    /**
     * 検索パスを追加
     */
    public function addPath($path)
    {
        $this->assets_path_list[] = $path;
    }


    /**
     * 実行
     * ===============================
     *
     * request()->getUri(); をファイル名として、
     * 登録されている検索パス以下から検索
     * 見つかったファイルの拡張子別の処理を行い、
     * そのまま出力をする
     *
     */
    public function run ( )
    {
        $req = $this->request();
        $res = $this->response();

        // ファイルを探す
        if ($this->getFile($req->getUri(), $path, $suffix)) {

            // 拡張子別の処理
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

            // 登録されてない拡張子の場合
            $res->header('Content-Type: text/text');
            echo file_get_contents($path);

        } else { // ファイルが見つからなければNOTFOUND処理へ
            $this->notfound($req->getUri());
        }
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
        $map      = ArrayHelper::get($this->ext_map,$ext,array(
            'suffix' => array($ext)
        ));

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

    public function notfound($uri) {
        $this->warn($uri."が見つかりませんでした。");
        $this->response()->status(404)->write('Not Found'."<br/>".$uri)->send();
    }
}
