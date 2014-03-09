<?php
/**
 * ログ
 */
namespace Seaf\Log;


/**
 * ログクラス
 */
class Log {

    // 呼び出しを許可するメソッド
    private static $methods = array(
        'emerg'    => Level::EMERGENCY,
        'alert'    => Level::ALERT,
        'critical' => Level::CRITICAL,
        'error'    => Level::ERROR,
        'warn'     => Level::WARNING,
        'info'     => Level::INFO,
        'debug'    => Level::DEBUG
    );

    private static $handlers = array();

    /**
     *
     */
    private function __construct ( ) {
    }

    /**
     * ハンドラを設定する
     */
    public static function registerHandler (Handler $handler) {
        $name = $handler->getName();
        self::$handlers[$name] = $handler;
        return $handler;
    }

    /**
     * ハンドラを取得する
     */
    public static function getHandler ($name) 
    {
        return self::$handlers[$name];
    }

    /**
     * ハンドラを解除する
     */
    public static function unregisterHandler (Handler $handler) {
        $name = $handler->getName();
        unset(self::$handlers[$name]);
        return $handler;
    }

    /**
     * ロガーを取得する
     */
    public static function post ($context, $logLv = Level::INFO) {
        $message = array_shift($context);

        $context = array(
            'level'   => Level::$map[$logLv],
            'message' => $message,
            'vars'    => $context,
            'time'    => time()
        );

        self::_post($context, $logLv);

    }
    public static function _post ($context, $logLv = Level::INFO) {
        foreach (self::$handlers as $handler) {
            $handler->post($context, $logLv);
        }
    }

    /**
     * PHPエラーのハンドリング
     */
    public static function register ( ) 
    {
        set_error_handler(function($no, $msg, $file, $line, $context){
            $level = Level::$php_error_map[$no];
            $context = array(
                'level'   => Level::$map[$level],
                'message' => $msg.' '.substr($file,-25).' '.$line,
                'context' => $context,
                'time'    => time(),
                'vars'    => array()
            );
            self::_post($context,$level);
        });
    }

    public static function __callStatic ($name, $params) 
    {
        if (array_key_exists($name, self::$methods)) {
            self::post($params,self::$methods[$name]);
        }
    }
}
