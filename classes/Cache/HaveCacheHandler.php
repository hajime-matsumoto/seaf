<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

trait HaveCacheHandler
    {
        private $cacheHandler;

        /**
         * キャッシュハンドラを取得する
         */
        public function getCacheHandler ( )
        {
            if ($this->hasCacheHandler()) {
                return $this->cacheHandler;
            }

            return $this->cacheHandler = new CacheHandler( );
        }
        /**
         * キャッシュハンドラを作成する
         */
        public function makeCacheHandler ($c)
        {
            $handler = new CacheHandler( );
            $handler->setStorage($c);
            return $handler;
        }

        /**
         * キャッシュハンドラを設定する
         */
        public function setCacheHandler (CacheHandler $handler)
        {
            $this->cacheHandler = $handler;
            return $this;
        }

        /**
         * キャッシュハンドラが設定されているか
         */
        public function hasCacheHandler ( )
        {
            return isset($this->cacheHandler) ? true: false;
        }
    }
