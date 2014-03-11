<?php
use Seaf\Core\Environment;

use Seaf\Core\ComponentManager;
use Seaf\Core\HelperManager;
/**
 * Seaf
 * ================================
 *
 * グローバルシングルトンインスタンス
 *
 */
class Seaf {

    /**
     * @var Seaf
     */
    private static $instance = false;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * シングルトン
     */
    public static function singleton ( ) {
        return self::$instance ? self::$instance: self::$instance = new Seaf();
    }

    private function __construct ( ) {
        $this->environment = new Environment( );

        // Seaf独自の処理を加える

        // ロガーの作成
        $this->register('log', 'Seaf\Log\Log', null, function($log) {
            $log->register();
        });

        // コンフィグをグローバルに作成
        ComponentManager::getGlobal()->register('config', 'Seaf\Config\Config');
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
