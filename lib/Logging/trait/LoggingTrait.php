<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logging;

use Seaf\Event;

trait LoggingTrait
    {
        abstract public function trigger($name, $params = []);

        /**
         * 緊急メッセージ
         */
        public function emerg ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::EMERGENCY, $message, $params, $tags));
            $this->trigger('log.emerg', ['log'=>$log]);
        }

        /**
         * alertメッセージ
         */
        public function alert ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::ALERT, $message, $params, $tags));
            $this->trigger('log.alert', ['log'=>$log]);
        }

        /**
         * 致命的なメッセージ
         */
        public function crit ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::CRITICAL, $message, $params, $tags));
            $this->trigger('log.crit', ['log'=>$log]);
        }

        /**
         * エラーメッセージ
         */
        public function error ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::ERROR, $message, $params, $tags));
            $this->trigger('log.error', ['log'=>$log]);
        }

        /**
         * ワーニングメッセージ
         */
        public function warn ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::WARNING, $message, $params, $tags));
            $this->trigger('log.warn', ['log'=>$log]);
        }

        /**
         * インフォメーションレベルのメッセージ
         */
        public function info ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::INFO, $message, $params, $tags));
            $this->trigger('log.info', ['log'=>$log]);
        }

        /**
         * debugメッセージ
         */
        public function debug ($message, $params = [], $tags = [])
        {
            $this->logPost($log = new Log(Code\LogLevel::DEBUG, $message, $params, $tags));
            $this->trigger('log.debug', ['log'=>$log]);
        }

        /**
         * ログを送出する
         */
        public function logPost(Log $log)
        {
            $this->trigger('log.post', ['log'=>$log]);
        }
    }
