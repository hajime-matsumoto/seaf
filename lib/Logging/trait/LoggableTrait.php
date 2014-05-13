<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Logging;

use Seaf\Base\Container;
use Seaf\Util\Util;

/**
 * 
 */
trait LoggableTrait
    {
        private $logHandler;

        abstract public function fireEvent ($type, $args = []);


        public function debug ($message, $tags = null, $params = [], $nest = 1)
        {
            return $this->logPost(
                Level::DEBUG,
                $message,
                $tags,
                $params,
                $nest
            );
        }

        public function info ($message, $tags = null, $params = [], $nest = 1)
        {
            return $this->logPost(
                Level::INFO,
                $message,
                $tags,
                $params,
                $nest
            );
        }

        public function warn ($message, $tags = null, $params = [], $nest = 1)
        {
            return $this->logPost(
                Level::WARNING,
                $message,
                $tags,
                $params,
                $nest
            );
        }

        public function crit ($message, $tags = null, $params = [], $nest = 1)
        {
            return $this->logPost(
                Level::CRITICAL,
                $message,
                $tags,
                $params,
                $nest
            );
        }

        public  function logHandler( ) 
        {
            if (!$this->logHandler) {
                $this->logHandler = new LogHandler($this);
            }
            return $this->logHandler;
        }

        private function logPost($level, $message, $tags, $params, $nest)
        {
            if ($tags == null) {
                if (method_exists($this, 'getObjectName')) {
                    $tags = [$this->getObjectName()];
                }
            }
            $log = new Log(
                $level,
                $message,
                $tags,
                $params,
                $nest+1
            );

            $this->fireEvent('log', [
                'log' => $log
            ]);
        }
    }
