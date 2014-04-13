<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;


trait StorageTrait
    {
        private $storageHandler = false;

        abstract public function seaf ( );

        public function setStorageHandler(Storage $handler)
        {
            $this->storageHandler  = $handler;
        }

        private function getStorageHandler($table, $type)
        {
            if ($this->storageHandler == false) {
                return $this->sf()->storage($table, $type);
            }else{
                return $this->storageHandler;
            }
        }

        private function storage($table, $type)
        {
            return $this->getStorageHandler($table, $type);
        }
    }

