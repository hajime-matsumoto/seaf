<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core;

/**
 * クラスファイルのパスを設定
 */
if (!defined('SEAF_CLASS_PATH')) define('SEAF_CLASS_PATH', realpath(__DIR__.'/../'));

/**
 * モジュールファイルのパスを設定
 */
if (!defined('SEAF_MODULE_PATH')) define('SEAF_MODULE_PATH', realpath(__DIR__.'/../../modules'));

/**
 * オートローダクラスを読み込む
 */
require_once SEAF_CLASS_PATH.'/Core/AutoLoader.php';
use Seaf\Core\AutoLoader;

/**
 * パターン
 */
require_once SEAF_CLASS_PATH.'/Base/Trait/SingletonTrait.php';
require_once SEAF_CLASS_PATH.'/Base/Trait/ComponentCompositeTrait.php';
use Seaf\Base;

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
    use Base\SingletonTrait;
    use Base\ComponentCompositeTrait;


    public static function who ( ) 
    {
        return __CLASS__;
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        // ローダーの起動
        $loader = AutoLoader::factory([
            'namespaces' => [
                'Seaf' => SEAF_CLASS_PATH
            ]
        ])->register();

        // コンポーネントコンテナを設定する
        $this->setComponentContainer('Seaf\Core\ComponentContainer');

        // コンポーネントへ登録
        $this->registerComponent('AutoLoader', $loader);

        //static::Cache()->enable = false;
    }

    /**
     * スタティックなコールはコンポーネントのヘルパへ
     */
    public static function __callStatic ($name, $args)
    {
        return static::singleton( )->__call($name, $args);
    }

    /**
     * コールはコンポーネントのヘルパへ
     */
    public function __call ($name, $args)
    {
        return $this->componentCall($name, $args);
    }

    /**
     * 初期化
     */
    public static function init ($root, $env = 'development')
    {
        static::Reg()->set('root', $root);
        static::Reg()->set('env', $env);
        static::Reg()->set('var_path', $root.'/var');

        // コンフィグをロードする
        // ---------------------------------------------------
        $cfg = static::Config();

        // コンフィグロード終了フラグを付ける
        static::Reg()->set('config_loaded', true);

        // PHPの挙動を定義する
        // ---------------------------------------------------
        mb_internal_encoding($cfg('encoding', 'default'));
        mb_language($cfg('lang', 'ja'));
        date_default_timezone_set($cfg('timezone', 'Asiz\Tokyo'));
        set_time_limit($cfg('time_lime', 60));

        error_reporting(E_ALL);
        ini_set('display_errors',1);

        // オートローダを設定
        // ---------------------------------------------------
        foreach($cfg('libraries',[]) as $lib) {
            static::AutoLoader()->addLibrary($lib);
        }

        // コンポーネント設定
        // ---------------------------------------------------
        static::singleton()->component()->loadConfig(
            $cfg('component', [])
        );

        // 終了イベントを発生させる
        static::Event()->trigger('post.init');

        // モジュール設定
        // ---------------------------------------------------

        // モジュールディレクトリ
        static::ModuleLoader()->addPath(SEAF_MODULE_PATH);

        foreach($cfg('startup.modules',[]) as $name) {
            static::singleton()->enmod($name);
        }
    }

    /**
     * モジュールを有効化
     */
    public function enmod($name)
    {
        $this->ModuleLoader()->enable($name);
    }

    /**
     * モジュールを有効化
     */
    public static function register($name, $define)
    {
        static::singleton()->component()->register($name, $define);
    }
}
