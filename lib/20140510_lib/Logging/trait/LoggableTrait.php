<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Logging;

use Seaf\Base\Container;

/**
 * 
 */
trait LoggableTrait
    {
        abstract public function fireEvent ($type, $args = []);

        public function debug ($code, $message, $tags = [], $params = [])
        {
            $log = new Log(
                Level::DEBUG,
                $code,
                $message,
                $tags = array_merge($tags, ['cache']),
                $params
            );

            $this->fireEvent('log', ['log'=>$log]);
        }

        public function info ($code, $message, $tags = [], $params = [])
        {
            $log = new Log(
                Level::INFO,
                $code,
                $message,
                $tags = array_merge($tags, ['cache']),
                $params
            );

            $this->fireEvent('log', ['log'=>$log]);
        }

        public function warn ($code, $message, $tags = [], $params = [])
        {
            $log = new Log(
                Level::WARNING,
                $code,
                $message,
                $tags = array_merge($tags, ['cache']),
                $params
            );

            $this->fireEvent('log', ['log'=>$log]);
        }

        public function crit ($code, $message, $tags = [], $params = [])
        {
            $log = new Log(
                Level::CRITICAL,
                $code,
                $message,
                $tags = array_merge($tags, ['cache']),
                $params
            );

            $this->fireEvent('log', ['log'=>$log]);
        }
    }
