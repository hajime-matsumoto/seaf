<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging\Formatter;

use Seaf\Logging;
use Seaf\Container;
use Seaf\Wrapper;

/**
 * ログフォーマッタ
 */
class TextFormatter extends Logging\Formatter
{
    private $format;
    private $params = [
        'message',
        'level',
        'params',
        'tags',
        'time'
    ];
    public function __construct ($cfg)
    {
        $this->format = $this->compile($cfg('format'));
    }

    private function compile($format)
    {
        return preg_replace_callback('/%([^%]*)%/',function($m) {
            return '%'.(1 + array_search($m[1],$this->params)).'$s';
        }, $format);
    }


    public function format (Logging\Log $log)
    {
        $message = $log->getMessage();
        $level   = $log->getLevelAsString();
        $params  = empty($log->params) ? '': print_r($log->params,true);
        $tags    = implode(',', $log->tags);
        $time    = $log->getTimeWithFormat('Y-m-d G:i:s');

        return trim(
            sprintf($this->format, $message, $level, $params, $tags, $time)
        );
    }
}
