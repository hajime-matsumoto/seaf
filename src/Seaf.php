<?php
namespace Seaf;

use Seaf\Kernel\Kernel;

/**
 * Seaf
 */
class Seaf
{
    const DEFAULT_LANG     = 'ja';
    const DEFAULT_TIMEZONE = 'Asia/Tokyo';

    /**
     * @var Seaf
     */
    private static $instance;
    private static $module_dirs = array();

    /**
     * @return Seaf
     */
    public static function singleton ()
    {
        if (self::$instance instanceof Seaf) {
            return self::$instance;
        }
        return self::$instance = new Seaf;
    }

    /**
     * init
     *
     * @param string $config コンフィグファイルパス
     * @return void
     */
    public static function init ($config = false)
    {
        self::singleton()->_init($config);
        return self::singleton();
    }

    /**
     * 初期化処理
     *
     * @param string $config コンフィグファイルパス
     * @return void
     */
    private function _init ($config)
    {
        // カーネルを初期化する
        Kernel::init();

        // コンフィグを読み込む
        $config = Kernel::DI()->config( )->load($config);

        // ディレクトリを作成する
        Kernel::fileSystem()
            ->mkdir((string) $config->get('dirs.tmp'), 01777)
            ->mkdir((string) $config->get('dirs.cache'), 01777)
            ->mkdir((string) $config->get('dirs.logs'), 01777);

        // Kernelにログハンドラを設定する
        $logger = Kernel::logger($config->get('logger')->toArray());

        // 終了処理を設定する
        register_shutdown_function(array('Seaf\Seaf','phpShutdownFunction'));

        // モジュールディレクトリを登録する
        self::addModuleDir(__DIR__.'/Module');
        foreach ($config->get('dirs.modules')->toArray() as $dir) {
            self::addModuleDir($dir);
        }

        // PHPエラーのハンドリングを開始する
        $logger('PHP')->register();

        // ログを書いてみる
        $logger('Seaf')->debug('起動');

        // モジュールを有効にする
        foreach ($config->get('modules')->toArray() as $mod) {
            Seaf::enmod($mod);
        }

        // 言語の設定
        mb_internal_encoding($config->get('encoding', 'utf-8'));
        mb_language($config->get('lang', self::DEFAULT_LANG));

        // タイムロケール
        date_default_timezone_set($config->get('timezone', self::DEFAULT_TIMEZONE));
    }

    /**
     * 終了処理
     */
    public static function phpShutdownFunction ( )
    {
        if (!is_null($e = error_get_last())) {
            Kernel::logger()->phpErrorHandler(
                $e['type'],
                $e['message'],
                $e['file'],
                $e['line'],
                null
            );
            Kernel::logger('Seaf')->debug("エラー終了しました");
        } else {
            Kernel::logger('Seaf')->debug("正常終了しました");
        }
    }


    /**
     * モジュールディレクトリを登録
     *
     * @param string
     */
    public static function addModuleDir ($dir)
    {
        self::$module_dirs[] = $dir;
    }

    /**
     * モジュールを有効化
     */
    public static function enmod ($mod)
    {
        $found = false;
        foreach (self::$module_dirs as $dir) {
            $file = Kernel::fileSystem($dir.'/'.ucfirst($mod).'/__autoload.php');
            if ($file->exists()) {
                $file->doRequire();
                $found = true;
                break;
            }
        }
        if ($found == false) {
            Kernel::logger('Seaf')->warning(array("%sの__autoload.phpが見つかりません",$mod));
        } else {
            Kernel::logger('Seaf')->debug(array("%sを有効にしました",$mod));
        }
    }
}
