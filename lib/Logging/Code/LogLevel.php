<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging\Code;


/**
 * ログレベルコードマップ
 */
class LogLevel {

    // ログレベル
    const EMERGENCY = 1;
    const ALERT     = 2;
    const CRITICAL  = 4;
    const ERROR     = 8;
    const WARNING   = 16;
    const INFO      = 32;
    const DEBUG     = 64;
    const ALL       = 127;


    /**
     * エラーラベルマップ
     */
    public static $map = array(
        self::EMERGENCY => 'EMERGENCY',
        self::ALERT     => 'ALERT',
        self::CRITICAL  => 'CRITICAL',
        self::ERROR     => 'ERROR',
        self::WARNING   => 'WARNING',
        self::INFO      => 'INFO',
        self::DEBUG     => 'DEBUG'
    );

    /**
     * PHPエラーマップ
     */
    public static $php_error_map = array(
        E_COMPILE_ERROR     => self::EMERGENCY,
        E_ERROR             => self::EMERGENCY,
        E_PARSE             => self::EMERGENCY,
        E_CORE_ERROR        => self::EMERGENCY,
        E_RECOVERABLE_ERROR => self::ALERT,
        E_USER_ERROR        => self::ERROR,
        E_WARNING           => self::WARNING,
        E_CORE_WARNING      => self::WARNING,
        E_COMPILE_WARNING   => self::WARNING,
        E_USER_WARNING      => self::WARNING,
        E_DEPRECATED        => self::WARNING,
        E_USER_DEPRECATED   => self::WARNING,
        E_NOTICE            => self::WARNING,
        E_USER_NOTICE       => self::WARNING,
        E_STRICT            => self::WARNING
    );

    public static function parse ($str)
    {
        $t = strtok($str, ' ');
        $next = '|';
        $int = 0;
        do {
            if (in_array($t, array('^','|'))) {
                $next = $t;
                continue;
            }

            if ($next == '|') {
                $int = $int | constant('self::'.strtoupper($t));
            } elseif ($next == '^') {
                $int = $int ^ constant('self::'.strtoupper($t));
            }

        } while($t = strtok(' '));

        return $int;
    }

    public static function convertPHPErrorCode($eno)
    {
        return isset(self::$php_error_map[$eno]) ?
            self::$php_error_map[$eno]:
            self::ERROR;
    }

    public static function convertLevelToString($code)
    {
        return isset(self::$map[$code]) ?
            self::$map[$code]:
            'UNDEFINED('.$code.')';
    }
}
