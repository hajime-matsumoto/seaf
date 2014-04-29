<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

trait ArrayContainerTrait
    {
        protected $data = [];

        public function __get($name)
        {
            return $this->getVar($name, null);
        }

        public function __set($name, $value)
        {
            return $this->setVar($name, $value);
        }

        /**
         * データをセットする
         */
        protected function initContainerData ($data) 
        {
            if (is_object($data)) {
                $data = $data->toArray();
            }
            $this->data = $data;
        }

        /**
         * データをリセットする
         */
        public function clearVars($data = [])
        {
            $this->data = $data;
        }

        /**
         * 配列にする
         */
        public function toArray ( ) 
        {
            return $this->data;
        }

        /**
         * セクションを取得する
         */
        public function section ($name)
        {
            return new ArrayContainerSection($this, $name);
        }

        /**
         * ヒットしたバリューに関数を適用する
         * 
         * @param string
         * @param callable
         * @return $this
         */
        public function mapVar($key, $callback)
        {
            $datas = $this->getVar($key, false);

            if (empty($datas)) return $this;

            if (!is_array($datas)) {
                $datas = [$datas];
            }

            foreach ($datas as $k => $v) {
                $callback($v, $k);
            }
            return $this;
        }
        /**
         * データをマージする
         */
        public function mergeVar($data)
        {
            $data = seaf_container($data);
            $this->data = array_merge($this->data, $data->data);
            return $this;
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
         * @return ArrayContainer
         */
        public function setVar ($key, $value = null)
        {
            if (is_array($key)) {
                foreach ($key as $k=>$v) {
                    $this->setVar($k, $v);
                }
                return $this;
            }
            ArrayHelper::set($this->data, $key, $value);
            return $this;
        }

        /**
         * データを追記する
         */
        public function appendVar($key, $value, $prepend = false)
        {
            $array = $this->getVar($key, []);
            $array[] = $value;
            $this->setVar($key, $array);
            return $this;
        }

        /**
         * データを取得してクリアする
         */
        public function getVarClear($key, $default = null)
        {
            $data = $this->getVar($key, $default);
            $this->delVar($key);
            return $data;
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
            return ArrayHelper::has($this->data, $key);
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
