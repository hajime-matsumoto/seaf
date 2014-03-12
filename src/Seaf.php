<?php

use Seaf\Kernel\Kernel;
use Seaf\Environment\Environment;
use Seaf\Environment\ComponentManager;

/**
 * Seaf
 */
class Seaf 
{
    const DEFAULT_LANG     = 'ja';
    const DEFAULT_TIMEZONE = 'Asia/Tokyo';

    /**
     * @var Environment
     */
    private static $env;

    /**
     * Global ComponentManager
     * @return ComponentManager
     */
    public static function GCM()
    {
        return ComponentManager::getGlobal();
    }

    /**
     * 初期化
     * ===================================
     * カーネルを初期化する
     * グローバルコンポーネントにConfigを設定
     * Loggerを設定
     */
    public static function init ( $config )
    {
        // 定数の補正
        if(!defined('SEAF_PROJECT_ROOT')) define('SEAF_PROJECT_ROOT', __DIR__);

        // カーネルを初期化する
        Kernel::init();

        // グローバルコンポーネントにConfigを追加する
        self::GCM()->register('config', 'Seaf\Config\Config')->setOpts($config);

        // グローバルコンポーネントにKernelを追加する
        self::GCM()->register('kernel', Kernel::singleton());

        // コンフィグを取得
        $config = self::GCM()->get('config');

        // キャッシュハンドラを追加する
        self::GCM()->register('cache', 'Seaf\Cache\Cache')->setOpts(array(
            'dir' => $config->dirs->cache
        ));

        // ディレクトリの存在を確かめる
        Kernel::fileSystem()
            ->mkdir($config->dirs->tmp, 01777)
            ->mkdir($config->dirs->logs, 01777)
            ->mkdir($config->dirs->cache, 01777);

        // Seaf用のEnvironmentを作成する
        self::$env = new Environment();

        // ロガーを登録する
        self::$env->di()->register('logger', 'Seaf\Log\Logger')->setOpts(array(
            'name'    => 'Seaf',
            'writers' => self::config('log')->toArray()
        ));

        // PHP Errorを補足する
        self::logger()->register();

        // 終了処理を設定する
        register_shutdown_function(array('Seaf','phpShutdownFunction'));

        self::logger()->debug("イニシャライズ開始");

        // モジュールを有効にする
        if (self::config('modules')) foreach (self::config('modules')->toArray() as $m)
        {
            self::enmod($m);
        }

        // 言語の設定
        mb_internal_encoding(self::config('encoding', 'utf-8'));
        mb_language(self::config('lang', self::DEFAULT_LANG));

        // タイムロケール
        date_default_timezone_set(self::config('timezone', self::DEFAULT_TIMEZONE));
    }

    /**
     * モジュールを有効化
     *
     * @param string $name
     * @return void
     */
    public static function enmod ($name)
    {
        $class = 'Seaf\\Module\\'.ucfirst($name);
        if (!class_exists($class)) {
            $class = 'Seaf\\Module\\'.ucfirst($name).'\\'.ucfirst($name);
        }
        Kernel::dispatch()->invokeStaticMethod($class,'register', self::$env);
    }


    /**
     * 終了処理
     */
    public static function phpShutdownFunction ( )
    {
        if (!is_null($e = error_get_last())) {
            self::logger()->phpErrorHandler(
                $e['type'],
                $e['message'],
                $e['file'],
                $e['line'],
                null
            );
            self::logger()->debug("エラー終了しました");
        } else {
            self::logger()->debug("正常終了しました");
        }
    }

    /**
     * スタティックな呼び出しはSeaf::$envへの呼び出しとする
     */
    public static function __callStatic($name, $params)
    {
        return self::$env->call($name, $params);
    }
}
