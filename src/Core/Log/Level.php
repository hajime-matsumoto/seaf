<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Log;


/**
 * ログレベルのマップ
 */
class Level {

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
}
