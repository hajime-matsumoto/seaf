<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;
namespace Seaf\Code;
namespace Seaf\Logging\Log;

trait LoggingTrait
    {
        /**
         * 緊急メッセージ
         */
        public function emerg ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::EMERGENCY, $message, $params, $tags))
        }

        /**
         * alertメッセージ
         */
        public function alert ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::ALERT, $message, $params, $tags))
        }

        /**
         * 致命的なメッセージ
         */
        public function crit ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::CRITICAL, $message, $params, $tags))
        }

        /**
         * エラーメッセージ
         */
        public function error ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::ERROR, $message, $params, $tags))
        }

        /**
         * ワーニングメッセージ
         */
        public function warn ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::WARNING, $message, $params, $tags))
        }

        /**
         * インフォメーションレベルのメッセージ
         */
        public function info ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::INFO, $message, $params, $tags))
        }

        /**
         * debugメッセージ
         */
        public function debug ($message, $params, $tags = [])
        {
            $this->logPost(new Log(Code\LogLevel::DEBUG, $message, $params, $tags))
        }

        /**
         * ログを送出する
         */
        public function logPost(Log $log)
        {
            var_dump($log);
        }
    }
