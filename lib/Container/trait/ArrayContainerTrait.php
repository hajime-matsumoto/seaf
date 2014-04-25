<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

trait ArrayContainerTrait
    {
        protected $data = [];

        /**
         * データをセットする
         */
        protected function initContainerData ($data) 
        {
            $this->data = $data;
        }

        /**
         * データを取得する
         *
         * @param string
         * @param mixed
         * @return mixed
         */
        public function getVar ($key, $default = null)
        {
            return ArrayHelper::get($this->data, $key, $default);
        }

        /**
         * データをセットする
         *
         * @param string
         * @param mixed
         * @return void
         */
        public function setVar ($key, $value = null)
        {
            ArrayHelper::set($this->data, $key, $value);
        }

        /**
         * データを削除する
         *
         * @param string
         * @return void
         */
        public function delVar ($key)
        {
            unset($this->data[$key]);
        }

        /**
         * データが存在するか
         *
         * @param string
         * @return bool
         */
        public function hasVar ($key)
        {
            ArrayHelper::has($this->data, $key);
        }


        // ----------------------------------
        // For ArrayAccess 
        // ----------------------------------

        /**
         * \ArrayAccess::offsetGet()
         */
        public function offsetGet ($offset)
        {
            return $this->getVar($offset);
        }

        /**
         * \ArrayAccess::offsetSet()
         */
        public function offsetSet ($offset, $value)
        {
            return $this->setVar($offset, $value);
        }

        /**
         * \ArrayAccess::offsetUnset()
         */
        public function offsetUnset ($offset)
        {
            return $this->delVar($offset);
        }

        /**
         * \ArrayAccess::offsetExists()
         */
        public function offsetExists ($offset)
        {
            return $this->hasVar($offset);
        }

        // ----------------------------------
        // For Iterator
        // ----------------------------------

        /**
         * \Iterator::current
         */
        public function current ( )
        {
            return $this->getVar($this->key());
        }

        /**
         * \Iterator::key
         */
        public function key ( )
        {
            return key($this->data);
        }

        /**
         * \Iterator::next
         */
        public function next ( )
        {
            return next($this->data);
        }

        /**
         * \Iterator::rewind
         */
        public function rewind ( )
        {
            reset($this->data);
        }

        /**
         * \Iterator::valid
         */
        public function valid ( )
        {
            if (current($this->data)) {
                return true;
            } else {
                return false;
            }
        }
    }
