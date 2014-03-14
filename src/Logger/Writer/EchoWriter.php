<?php
namespace Seaf\Logger\Writer;

use Seaf\Logger\Writer;

/**
 * ログをファイルに書きむクラス
 */
class EchoWriter extends Writer
{
    public $fileName, $mode;

    public function initWriter()
    {
    }

    protected function _makeMessage($context, $level)
    {
        $tag     = $context['tag'];
        $message = $context['message'];
        $vars    = $context['vars'];
        $time    = $context['time'];
        $trace   = $context['trace'];

        if (!empty($trace)) {
            $current = $trace[0];
            if (isset($current['file'])) {
                $file = $current['file'];
                $line = $current['line'];

                $message .= " ...".substr($file,-30)." ".$line;
            }
        }

        if (!empty($vars)) {
            $message .= print_r($vars, true);
        }
        return $this->makeMessage($message, $tag, $time, $level);
    }

    public function _post($context, $level)
    {
        echo $this->_makeMessage($context,$level);
        echo "\n";
    }
}
