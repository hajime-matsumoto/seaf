<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Http;


use Seaf\Seaf;
use Seaf\Http\WebApp;
use Seaf\Collection\ArrayCollection;

/**
 * アセット管理クラス
 *
 * このクラスをコンパイルの関係で
 * 以下のライブラリに依存しています。
 *
 * - sass (ruby)
 * - coffee (nodejs)
 * - uglifycss (nodejs)
 * - uglifyjs (nodejs)
 */
class AssetManager extends WebApp
{
    public static $img_exts = array(
        'git','jpg','jpeg','png','ico','bmp'
    );

    /**
     * アセット検索パスを設定
     */
    private $paths = array();

    public function __construct( $config  = array())
    {
        parent::__construct();

        if( is_array($config) && !empty($config) )
        {
            $config = new ArrayCollection($config);
            if( $config->has('paths') ) foreach($config->get('paths') as $path) {
                $this->addPath($path);
            }
        }
        else
        {
            // 実行されたディレクトリをパスに追加
            $this->addPath(getcwd());
        }


        //
        // 設定
        //

        // Sassのコンパイルコマンド
        $this->registry()->set('sass.cmd', 'sass --compass --no-cache -s');
        $this->registry()->set('sass.min.cmd', 'sass --compass --no-cache -s | uglifycss');

        // coffeeのコンパイルコマンド
        $this->registry()->set('coffee.cmd', 'coffee -p -s');
        $this->registry()->set('coffee.min.cmd', 'coffee -p -s | uglifyjs');

        // 
        // ルーティング
        //
        $this->router()->addRoute('/@path:*', array($this, 'index'));
    }

    /**
     * ファイルを受け付けてコンパイル後のデータを返す
     */
    public function index( $filePath )
    {
        // 拡張子を判定
        $ext = "";
        $this->trimExt( $filePath, $ext);

        if( $ext == "css" ) return $this->indexCss($filePath);
        if( $ext == "js" ) return $this->indexJs($filePath);

        // 画像だったら。
        if( in_array(strtolower($ext),self::$img_exts) )
        {
            return $this->indexImage($filePath);
        }
    }

    /**
     * 画像に対する処理
     */
    public function indexImage($filePath)
    {
        $ext = "";
        $img = $this->trimExt($filePath, $ext);

        $file = $this->findFile( $img, array($ext));

        if( !$file ) return true;

        $finfo = finfo_open(FILEINFO_MIME);
        $mime  = finfo_file($finfo,$file);
        finfo_close($finfo);
        
        Seaf::http()
            ->response()
            ->status(200)
            ->reset()
            ->header('Content-Type', $mime)
            ->write(file_get_contents())
            ->send();
    }

    /**
     * JSに対する処理
     */
    public function indexJs($filePath)
    {
        // 拡張子を取り除く
        $ext = "";
        $filePath = $this->trimExt( $filePath, $ext);

        $js = $this->findJs($filePath);
        $this->trimExt($js, $ext); // 見つかったファイルの拡張子を取得

        if( !$js ) return true;



        // コンパイル
        $stdout = $this->compile( $this->registry()->get('coffee.cmd'), $js );

        Seaf::http()
            ->response()
            ->status(200)
            ->reset()
            ->header('Content-Type','text/javascript')
            ->write( $stdout )
            ->send();
    }


    /**
     * CSSに対する処理
     */
    public function indexCss($filePath)
    {
        // 拡張子を取り除く
        $ext = "";
        $filePath = $this->trimExt( $filePath, $ext);

        // CSSファイルを探す
        // .sass .scss を探す
        $sass = $this->findCss($filePath);

        if( !$sass ) return true;

        $loadPath = '';
        foreach( $this->paths as $path )
        {
            $loadPath.= ' -I '.realpath($path);
        }

        // コンパイル
        $stdout = $this->compile( $this->registry()->get('sass.cmd').$loadPath, $sass );

        Seaf::http()
            ->response()
            ->status(200)
            ->reset()
            ->header('Content-Type','text/css')
            ->write( $stdout )
            ->send();
    }

    /**
     * 検索パスを追加する
     */
    public function addPath( $path )
    {
        $this->paths[] = $path;
    }

    /**
     * JSファイルを探す
     *
     * @param string $fileName ファイル名
     * @return mixed 見つかったファイルのフルパスなければfalse
     */
    public function findJs( $fileName )
    {
        $fileNameNoExt = $this->trimExt( $fileName, $ext );

        return $this->findFile( $fileNameNoExt, array('coffee','js') );
    }

    /**
     * CSSファイルを探す
     *
     * @param string $fileName ファイル名
     * @return mixed 見つかったファイルのフルパスなければfalse
     */
    public function findCss( $fileName )
    {
        $fileNameNoExt = $this->trimExt( $fileName, $ext );

        return $this->findFile( $fileNameNoExt, array('sass','scss','css') );
    }

    /**
     * ファイルを探す
     */
    public function findFile( $fileNameNoExt, $exts = array() )
    {
        foreach($this->paths as $path )
        {
            foreach( $exts as $ext )
            {
                $file = rtrim($path,'/').'/'.ltrim($fileNameNoExt,'/').".".$ext;
                if(file_exists($file)) return $file;
            }
        }
        return false;
    }


    /**
     * コンパイルをする
     *
     * @param string $cmd 実行コマンド
     * @param mixed $input ソースファイルパス,複数与えれば結合してコンパイルする
     * @return string コンパイル後のソース
     */
    protected function compile( $cmd, $inputFiles)
    {
        $desc = array(
            0 => array("pipe","r"), // 子プロセスに読ませる
            1 => array("pipe","w"), // 子プロセスに書かせる
            2 => array("pipe","w")  // 子プロセスに書かせる
        );

        Seaf::debug("コマンドを実行します。：".$cmd);
        $proc = proc_open($cmd, $desc, $pipes);

        // 標準入力へデータを書き込む
        if( !is_array( $inputFiles ) )
        {
            $inputFiles = array($inputFiles);
        }
        foreach( $inputFiles as $inputFile )
        {
            fwrite($pipes[0], file_get_contents($inputFile));
        }
        fclose($pipes[0]);

        // 標準出力を取得
        $output = stream_get_contents($pipes[1]);

        // 標準エラー出力を取得
        $stderr = stream_get_contents($pipes[2]);

        if(strlen($stderr)) {
            Seaf::warn("エラーが発生したコマンド：".$cmd."：".$stderr);
        }

        // 終了処理
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proc);

        return $output;
    }

    /**
     * 拡張子を取得する
     */
    protected function trimExt( $file, &$ext )
    {
        if( false !== ($p = strrpos($file, '.')) )
        {
            $ext = strtolower(substr($file,$p+1));
            return substr($file,0,$p);
        }
        return $file;
    }
}
