<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Logging;

use Seaf\Util\Util;

/**
 * 
 */
class Log
{
    public function __construct ($level, $message, $tags = [], $params = [], $nest = 1)
    {
        $this->data = Util::Dictionary([
            'tags' => Util::SeparatedString("|", null, ">"),
            'time' => time()
        ]);

        $this->setLevel($level);
        $this->setMessage($message);
        $this->setTag($tags);
        $this->setParam($params);
        $this->setNest($nest);


        $nest = $this->data->get('nest');
        $traces = debug_backtrace(false, $this->data->get('nest'));
        $trace = Util::Dictionary(array_pop($traces));
        $this->setFile($trace['file'], $trace['line']);

    }

    public function setFile($file, $line)
    {
        $this->data->set('file', $file);
        $this->data->set('line', $line);
    }

    public function setNest($nest)
    {
        $this->data->set('nest', $nest+1);
    }

    public function setLevel($level)
    {
        if (is_int($level)) {
            $this->data->set('level', $level);
            return;
        }
        $this->data->set(Level::parse($level));
    }

    public function setMessage($message)
    {
        if (is_array($message)) {
            $format = array_shift($message);
            $message = vsprintf($format, $message);
        }

        $this->data->set('message', $message);
    }

    public function setTag($tag)
    {
        $this->data->tags->init($tag);
    }
 
    public function setParam($params)
    {
        $this->data->dict('params')->init($params);
    }

    public function getMessage( )
    {
        return $this->data->message;
    }

    public function get($name)
    {
        return $this->data->get($name);
    }

    public function __toString( )
    {
        return Formatter\DefaultFormatter::toString($this);
    }
}
