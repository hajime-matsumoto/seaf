<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

/**
 * ログデータ
 */
class Log
{
    /**
     * コンストラクタ
     */
    public function __construct ($level, $message, $params, $tags)
    {
        $this->level   = $level;
        $this->message = $message;
        $this->params  = $params;
        $this->tags    = is_string($tags) ? [$tags]: $tags;
        $this->time    = time();
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
}
