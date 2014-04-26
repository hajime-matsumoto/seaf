<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

trait CacheUserTrait
    {
        private $cacheHandler;

        /**
         * キャッシュハンドラを設定する
         *
         * @param CacheHandler
         */
        public function setCacheHandler(CacheHandler $handler)
        {
            $this->cacheHandler = $handler;
        }

        /**
         * キャッシュハンドラを取得する
         *
         * @return CacheHandler
         */
        public function getCacheHandler( )
        {
            if (isset($this->cacheHandler)) {
                return $this->cacheHandler;
            }
            return CacheHandler::getSingleton( );
        }
    }
