<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\KeyValueStore;

trait KVSUserTrait
    {
        private $kvsHandler;

        /**
         * ハンドラを設定する
         *
         * @param KVSHandler
         */
        public function setKVSHandler(KVSHandler $handler)
        {
            $this->kvsHandler = $handler;
        }

        /**
         * ハンドラを取得する
         *
         * @return KVSHandler
         */
        public function getKVSHandler( )
        {
            if (isset($this->kvsHandler)) {
                return $this->kvsHandler;
            }
            return KVSHandler::getSingleton( );
        }
    }
