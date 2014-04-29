<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Cache;

trait CacheUserTrait
    {
        private $cacheHandler;
        protected $cacheKey = null;

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
        public function getCacheHandler($cacheKey = '')
        {
            if (empty($cacheKey) && $this->cacheKey != null) {
                $cacheKey = $this->cachekey;
            }

            if (isset($this->cacheHandler)) {
                return $this->cacheHandler;
            }
            if ($cacheKey == null) {
                return CacheHandler::getSingleton( );
            }
            return CacheHandler::getSingleton( )->section($cacheKey);
        }

        /**
         * キャッシュキーをセット
         */
        public function setCacheKey($key)
        {
            $this->cacheKey = $key;
        }
    }
