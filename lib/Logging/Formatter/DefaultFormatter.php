<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * ロギングモジュール
 */
namespace Seaf\Logging\Formatter;

use Seaf\Logging\Level;
use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\ConfigureTrait;

class DefaultFormatter
{
    use ConfigureTrait;


    /**
     * コンストラクタ
     */
    public function __construct (array $setting)
    {
        $this->configure($setting, [
            'file_len' => 50
        ]);
    }

    public static function toString($log)
    {
        $formatter = new self([]);
        return $formatter->format($log);
    }

    public function format($log)
    {
        $message = $log->get('message');
        $level = $log->get('level');

        // レベル別装飾
        $head = '';
        switch($level) {
        case Level::EMERGENCY:
        case Level::ALERT:
        case Level::CRITICAL:
        case Level::ERROR:
        case Level::WARNING:
            $head = "!!!";
            break;
        case Level::INFO:
            $head = "===";
            break;
        case Level::DEBUG:
            $head = "###";
            break;
        }

        $label = substr(Level::convertLevelToString($level),0,4);

        // 複数行か単数行か
        $parts = explode("\n", $message);

        $sep = ' - ';

        // 単数行
        if (count($parts) == 1) {
            $text = $head.' '.$label;
            $text.= $sep;
            $text.= $log->get('tags')->last();
            $text.= $sep;
            $text.= $message;
        }else{
            $text = $head.' '.$label;
            $text.= $sep;
            $text.= $log->get('tags')->last();
            $text.= $sep;
            $text.= implode("\n".str_repeat(" ", strlen($text)), $parts);
        }

        return $text;
    }
}
