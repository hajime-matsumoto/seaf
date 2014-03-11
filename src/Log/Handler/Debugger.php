<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Log\Handler;

use Seaf\Log;
use Seaf\Log\Level;

/**
 * デバッグ用ハンドラー（コールバック)
 */
class Debugger extends Log\Handler {


    public function __construct ($config) 
    {
        parent::__construct($config);

        $this->level = Level::ALL ^ Level::INFO;
    }

    public function _post ($context, $level = Log\Level::INFO) 
    {
        $msg = $context['message'];
        if (is_object($msg)) $msg = 'object:'.get_class($msg);
        $vars = $context['vars'];

        if (!empty($vars)) {
            var_dump($vars);
        }

        echo "\n[".level::$map[$level]."] ".$msg."\n";

        $trace = debug_backtrace(false);
        $trace = array_slice($trace, 6,count($trace));

        foreach ($trace as $k=>$bt) {
            extract($bt);
            if (isset($file)) {
                $file = substr($file,-30);
            }else{
                $file = $line = "";
            }
            if(!isset($class)) $class="";
            if(!isset($type)) $type="";
            if(!isset($function)) $function="";
            echo '# '.$k." ".$class.$type.$function." ".$file." ".$line;
            echo "\n";
        }

    }
}
