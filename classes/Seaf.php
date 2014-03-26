<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

/**
 * クラスファイルのパスを設定
 */
if (!defined('SEAF_CLASS_PATH')) define('SEAF_CLASS_PATH', __DIR__);

/**
 * モジュールファイルのパスを設定
 */
if (!defined('SEAF_MODULE_PATH')) define('SEAF_MODULE_PATH', realpath(__DIR__.'/../modules'));

/**
 * オートローダクラスを読み込む
 */
require_once SEAF_CLASS_PATH.'/Core/Component/AutoLoader.php';

use Seaf\Core\Component\AutoLoader;

/**
 * Seafクラス
 *
 * シングルトンパターンのクラスで、一つの環境オブジェクトを保持する。
 * 結果、環境オブジェクトはグローバルな設定や、グローバルなコンポーネント
 * を保持し、アクセッサの役割を果たす。
 * 主な使い方は、スタティックなメソッドをコールする事で、シングルトンの
 * 保持する環境に処理をさせる。
 *
 * 起動時に設定を読み、グローバルな初期処理を行う
 *
 * <code>
 * Seaf::Logger()->debug
 * Seaf::FileSystem('some/dir/')->get('somefile')->getContents();
 * </code>
 */
class Seaf
{
    /**
     * プロジェクトのルート
     * @var string
     */
    public static $root_path;

    /**
     * コンフィグファイルパス
     * @var string
     */
    public static $config_path = '/etc/setting.yaml';

    /**
     * プロダクションモード {development|production}
     * @var string
     */
    public static $production_mode;

    /**
     * 初期化フラグ
     * @var bool
     */
    private static $initialized = false;

    /**
     * インスタンスの保持
     * @var Seaf
     */
    private static $instance = false;

    /**
     * インスタンスの取得処理
     *
     * @return Seaf
     */
    public static function singleton ( )
    {
        return self::$instance ? self::$instance: self::$instance = new self();
    }

    /**
     * 初期化処理
     *
     * @param string Project Root Path
     * @param string Production Mode 
     */
    public static function init ($root, $env = 'development')
    {
        if (self::$initialized == true) return self::singleton();
        self::$initialized = true;

        self::$root_path       = $root;
        self::$production_mode = $env;

        // オートローダの起動
        $autoloader = AutoLoader::factory(array(
            'namespaces' => array(
                'Seaf' => SEAF_CLASS_PATH
            )
        ))->register();

        $seaf = self::singleton();
        $seaf->register('autoLoader', $autoloader);

        // コンフィグを読み込む
        $c = $seaf->config()->load($root.'/'.self::$config_path);

        // 言語とタイムゾーンの設定
        mb_internal_encoding($c('encoding','utf-8'));
        mb_language($c('lang','ja'));
        date_default_timezone_set($c('timezone','Asia\Tokyo'));

        // コンポーネントを一括で設定する
        $seaf->di( )->factory->setFactoryConfigs($c('seaf.factory', array()));

        // ロガーを起動する
        $seaf->logger()->register();

        // ライタブルディレクトリ
        foreach (array(
            $c('dirs.tmp'),
            $c('dirs.cache'),
            $c('dirs.logs')
        ) as $dir) {
            $dir = $seaf->fileSystem()->mkdir($dir, 01777);

            if (!$dir->isWritable()) { // 書き込めなければ警告を吐く
                $seaf->logger()->warn(array(
                    "%sに書き込めません",
                    $dir
                ));
            }

            if (!$dir->isDir()) { // ディレクトリでなければ警告を吐く
                $seaf->logger()->warn(array(
                    "%sはディレクトリではありません",
                    $dir
                ));
            }
        }
        // モジュールの処理
        foreach($c('module.enabled') as $mod_name)
        {
            $seaf->enmod($mod_name);
        }
    }

    /**
     * モジュールを有効化する
     *
     * @param string
     */
    public function _enmod ($mod)
    {
        $c = Seaf::Config();

        foreach ($c('module.dirs') as $dir) {
            $script = Seaf::FileSystem($dir.'/'.$mod.'/__module.php');
            if ($script->isExists()) {
                $script->requireOnce( );
            }
        }
    }

    /**
     * コンストラクタ
     */
    private function __construct ( )
    {
        $this->environment = new Seaf\Core\Environment();

        $this->bind($this, array(
            'enmod' => '_enmod'
        ));
    }

    /**
     * スタティックコール
     *
     * @param string
     * @param array
     * @return mixed
     */
    public static function __callStatic ($name, $params)
    {
        return self::singleton()->environment->call($name, $params);
    }

    /**
     * コール
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call ($name, $params)
    {
        return $this->environment->call($name, $params);
    }
}
