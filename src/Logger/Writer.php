<?php
namespace Seaf\Logger;

use Seaf\Pattern\Configure;

/**
 * Logライター
 */
class Writer
{
    use Configure;

    const WRITER_CLASS_FORMAT = 'Seaf\Logger\Writer\%sWriter';

    private static $default_config = array (
        'type' => 'display',
        'level' => Level::ALL
    );

    /**
     * __construct
     *
     * @param $config
     */
    public function __construct ($config)
    {
        $this->configure($config);
        $this->initWriter();
    }

    public function __destruct ( )
    {
        $this->shutdownWriter( );
    }

    public function initWriter () 
    {
    }

    public function shutdownWriter () 
    {
    }

    /**
     * post
     *
     * @param $message, $vars, $level
     * @return void
     */
    public function post ($context, $level)
    {
        if ($this->level & $level) {
            $this->_post($context, $level);
        }
    }

    /**
     * makeMessage
     *
     * @param $message, $time, $level
     * @return void
     */
    public function makeMessage ($message, $tag, $time, $level)
    {
        $time = date('Y-m-d G-i-s', $time);
        $level = Level::$map[$level];

        return "[$level] [$tag] $time $message";
    }

    public function setLevel($level)
    {
        if (is_int($level)) 
        {
            $this->level = $level;
            return;
        }

        $token = strtok($level, ' ');
        $level_int = 0;
        $enzan = null;
        do {
            if (!in_array($token, array('^','|'))) {
                $int = constant('Seaf\Logger\Level::'.strtoupper($token));

                if ($enzan == false) {
                    $level_int = $int;
                } elseif ($enzan == '^') {
                    $level_int = $level_int ^ $int;
                } elseif ($enzan == '|') {
                    $level_int = $level_int | $int;
                }
            } else {
                $enzan = $token;
            }
        } while ($token = strtok(' '));
        $this->level = $level_int;
        return;
    }

    /**
     * factory
     *
     * @param $config
     */
    public static function factory ($config)
    {
        $type = self::getConfig($config, 'type');
        $class = sprintf(self::WRITER_CLASS_FORMAT,ucfirst($type));

        return new $class(array_merge(self::$default_config,$config));
    }

    public static function getConfig($config, $name)
    {
        if (isset($config[$name])) return $config[$name];

        return self::$default_config[$name];
    }
}
