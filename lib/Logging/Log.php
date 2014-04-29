<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

/**
 * ログデータ
 */
class Log
{
    public $tags = [];
    public $params = [];
    public $message;
    public $level;
    public $time;
    /**
     * コンストラクタ
     */
    public function __construct ($level, $message, $params, $tags = [])
    {
        $this->level   = $level;
        $this->message = is_array($message) ? 
            vsprintf($message[0], array_slice($message,1)):
            $message;
        $this->params  = $params;
        $this->tags    = is_string($tags) ? [$tags]: $tags;
        $this->time    = time();
    }

    public function __toString ( )
    {
        return (string) $this->toString();
    }

    public function addTag($tag, $prepend = false)
    {
        if ($prepend == false) {
            $this->tags[] = $tag;
        }else{
            array_unshift($this->tags, $tag);
        }
        return $this;
    }

    public function hasTag($tag)
    {
        return in_array($tag, $this->tags);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getLevelAsString()
    {
        return Code\LogLevel::convertLevelToString($this->level);
    }

    public function getTimeWithFormat($format)
    {
        return date($format, $this->time);
    }

    public function toString( )
    {
        $format = new Formatter\TextFormatter([]);
        return $format->format($this);
    }
}
