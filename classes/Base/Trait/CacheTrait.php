<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;


trait CacheTrait
    {
        private $cacheHandler = false;
        abstract public function seaf ( );

        public function getCacheHandler( )
        {
            $prefix = str_replace('\\','_',get_class($this));

            if ($this->cacheHandler == false) {
                return $this->sf()->cache($prefix);
            }else{
                return $this->cacheHandler;
            }
        }

        public function saveCache($key, $data, $expire = 0)
        {
            $handler = $this->getCacheHandler();
            $handler->create($key, $data, $expire);
        }

        public function getCache($key, $unless = 0, $default = null)
        {
            $handler = $this->getCacheHandler();

            if ($handler->has($key, $unless)) {
                return $handler->get($key, $stat);
            }
            return $default;
        }

        public function useCache($key, $callback, $expire = 0, $unless = 0, &$stat = null)
        {
            $handler = $this->getCacheHandler();

            if ($handler->has($key, $unless)) {
                return $handler->get($key, $stat);
            } else {
                $data = $callback($isSuccess);
                if ($isSuccess !== false) {
                    $handler->create($key, $data, $expire);
                }
                $stat = false;
                return $data;
            }
        }

        public static function useCacheStatic ($prefix, $key, $callback, $expire = 0, $unless = 0, &$stat = null)
        {
            $handler = \Seaf::Cache($prefix);

            if ($handler->has($key, $unless)) {
                return $handler->get($key, $stat);
            } else {
                $data = $callback($isSuccess);
                if ($isSuccess !== false) {
                    $handler->create($key, $data, $expire);
                }
                $stat = false;
                return $data;
            }
        }
    }
