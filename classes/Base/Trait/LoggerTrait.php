<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

use Seaf\Logger;
use Seaf\Logger\LogLevel;

trait LoggerTrait
    {
        private $logHandler = false;

        abstract protected function seaf();

        protected function debug ($data)
        {
            $this->logHandler( )->post(LogLevel::DEBUG, $data);
        }

        protected function error ($data)
        {
            $this->logHandler( )->post(LogLevel::ERROR, $data);
        }

        protected function logHandler ( )
        {
            if ($this->logHandler) return $this->logHandler;
            return $this->seaf()->Logger(get_class($this));
        }
    }
