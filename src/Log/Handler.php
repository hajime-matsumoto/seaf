<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Log;


/**
 * ロギングハンドラ‐
 */
class Handler {
    const NAMESPACE_SEPARATOR = '\\';

    // 記録するレベル
    protected $level = Level::ALL;

    // ハンドラのデフォルト値
    protected static $default = array(
        'type' => 'console',
        'level' => Level::ALL
    );

    protected $timeFormat = 'Y-m-d H-i-s';
    protected $logFormat = '[%level%] %name% %time% %message%';


    public function __construct ($config) 
    {
        $this->config = $config;
        $this->level = $config['level'];
    }


    public function post ($context, $level = Level::INFO) {

        if ($this->level & $level) {
            $this->_post ($context, $level);
        }
    }

    public static function factory ($config) {

        $config = array_merge(self::$default, $config);

        $type = $config['type'];
        $class = __NAMESPACE__.self::NAMESPACE_SEPARATOR
            .'Handler'.self::NAMESPACE_SEPARATOR.ucfirst($type);

        return new $class($config);
    }

    public function makeMessage($context) {
        $level   = $context['level'];
        $message = $context['message'];
        $time    = date($this->timeFormat, $context['time']);
        $vars    = $context['vars'];
        $name    = $context['name'];

        if (is_object($message)) $message = get_class($message);

        return str_replace(
            array(
                '%level%',
                '%name%',
                '%time%',
                '%message%'
            ),
            array(
                $level,
                $name,
                $time,
                $message
            ),
            $this->logFormat
        );
    }

}
