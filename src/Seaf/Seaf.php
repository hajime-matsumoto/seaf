<?php
/**
 * Seaf
 */

use Seaf\Environment\Environment;

use Seaf\Log;

/**
 * Seaf
 * ================================
 *
 * グローバルシングルトンインスタンス
 *
 */
class Seaf {

    private static $instance = false;
    private static $isInitialized = false;

    private $environment;

    /**
     * シングルトン
     */
    public static function singleton ( ) {
        return self::$instance ? self::$instance: self::$instance = new Seaf();
    }

    private function __construct ( ) {
        $this->environment = new Environment( );

        // ログハンドラの管理
        $this->bind($this, array(
            'logHandler' => '_logHandler'
        ));
    }

    /**
     * 初期処理
     */
    public static function init ( )
    {
        if (self::$isInitialized) return self::singleton();
        self::$isInitialized = true;

        // PHPのエラーをキャプチャする
        Log\Log::register();

        // デフォルトエラーハンドラをセットする
        self::logHandler('default', array(
            'type' => 'console'
        ))->register();

    }

    /**
     * ログハンドラを設定/取得する
     *
     * @param string
     * @param array
     */
    public function _logHandler ($name = 'default', $config = null) 
    {
        if ($config == null) {
            return Log\Log::getHandler($name);
        }

        $config['name'] = $name;
        return Log\Handler::factory($config)->register();
    }

    /**
     * 処理はEnvironmentに任せる
     *
     * @param string
     * @param array
     */
    public function __call($name, $params) {
        return $this->environment->call($name, $params);
    }

    /**
     * 処理はEnvironmentに任せる
     *
     * @param string
     * @param array
     */
    public static function __callStatic($name, $params) 
    {
        return self::singleton()->environment->call($name, $params);
    }

}
