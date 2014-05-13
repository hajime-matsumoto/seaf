<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Logging;

use Seaf\Base\Container;

/**
 * 
 */
class Log extends Container\ArrayContainer
{
    public function __construct ($level, $code, $message, $tags = [], $params = [])
    {
        $this->setLevel($level);
        $this->setCode($code);
        $this->setMessage($message);
        $this->setTag($tags);
        $this->setParam($params);

        $this->set('time', time());
    }

    public function setLevel($level)
    {
        if (is_int($level)) {
            $this->set('level', $level);
            return;
        }
        $this->set(Level::parse($level));
    }

    public function setCode($code)
    {
        $this->set('code', $code);
    }

    public function setMessage($message)
    {
        if (is_array($message)) {
            $format = array_shift($message);
            $message = vsprintf($format, $message);
        }
        $this->set('message', $message);
    }

    public function setTag($tag)
    {
        $this->tags->data = $tag;
    }
 
    public function setParam($params)
    {
        $this->params->data = $params;
    }

    public function getMessage( )
    {
        return sprintf(
            "(%s) : %s : %s",
            substr(
                Level::convertLevelToString($this('level')),0,4
            ),
            $this('code'),
            $this('message')
        );
    }
}
