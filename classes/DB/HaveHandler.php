<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * DBハンドラを所有する
 */
trait HaveHandler
    {
        /**
         * データベースハンドラ
         *
         * @var Handler
         */
        private $handler;

        /**
         * データベースハンドラをセット
         *
         * @param Handler
         */
        public function setHandler ($handler)
        {
            $this->handler = $handler;
        }

        /**
         * データベースハンドラを取得
         *
         * @return Handler
         */
        public function getHandler ( )
        {
            return $this->handler;
        }

        /**
         * データベースハンドラがあるか
         *
         * @return bool
         */
        public function haveHandler ( )
        {
            return $this->handler ? true: false;
        }
    }
