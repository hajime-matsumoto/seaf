<?php
use Seaf\Core\Environment;

/**
 * Seaf
 * ================================
 *
 * グローバルシングルトンインスタンス
 *
 */
class Seaf {
    const ENV_DEVELOPMENT='development';
    const ENV_PRODUCTION='production';

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
