<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

use Seaf\Util\Util;

/**
 * 
 */
trait ArrayContainerTrait
    {
        protected $CASE_SENSITIVE_FLAG = true;
        protected $data = [];

        /**
         * キーの大文字小文字を区別
         */
        private function caceSensitive($flag = true)
        {
            $this->CASE_SENSITIVE_FLAG = $flag;
        }

        /**
         * キーのノーマライズ
         */
        protected function nameNormalizer ($name)
        {
            if ($this->CASE_SENSITIVE_FLAG == false)
            {
                return strtolower($name);
            }
            return $name;
        }

        /**
         * 値を格納する
         *
         * @param string|array
         * @param mixed
         * @return $this
         */
        public function set($name, $value = null)
        {
            if (is_array($name)) {
                foreach ($name as $k=>$v) {
                    $normal_name = $this->nameNormalizer($k);
                    $this->data[$normal_name] =& $name[$k];
                }
                return $this;
            }

            $name = $this->nameNormalizer($name);
            $this->data[$name] = $value;
            return $this;
        }

        /**
         * 値を取得する
         *
         * @param string
         * @param mixed
         * @return mixed
         */
        public function get($name, $default = null)
        {
            $name = $this->nameNormalizer($name);
            return isset($this->data[$name]) ?
                $this->data[$name]:
                $default;
        }

        /**
         * 値を削除する
         *
         * @param string
         * @param mixed
         * @return $this
         */
        public function clear($name)
        {
            $name = $this->nameNormalizer($name);
            unset($this->data[$name]);
            return $this;
        }

        /**
         * 値が存在するか
         *
         * @param string
         * @return bool
         */
        public function has($name)
        {
            $name = $this->nameNormalizer($name);
            return array_key_exists($name, $this->data);
        }

        /**
         * 値が空値か
         *
         * @param string
         * @return bool
         */
        public function isEmpty($name = null)
        {
            if ($name == null) {
                return empty($this->data);
            }
            $name = $this->nameNormalizer($name);
            if (isset($this->data[$name]) && !empty($this->data[$name])) {
                if ($this->data[$name] instanceof ContainerIF) {
                    return $this->data[$name]->isEmpty();
                }
                return false;
            }
            return true;
        }

        /**
         * ノーマルな配列に変換
         *
         * @param string
         * @return bool
         */
        public function toArray ($name  = null)
        {
            if ($name == null) {
                return $this->data;
            }

            $data = $this->get($name, []);

            if (is_string($data)) {
                return $data = [$data];
            }
            return $data;
        }

        /**
         * ダンプ
         *
         * @param string
         * @return bool
         */
        public function dump ($useReturn = false, $level = 5)
        {
            if (is_int($useReturn)) {
                $level = $useReturn;
                $useReturn = false;
            }
            return Util::dump($this, $useReturn, $level);
        }

        /**
         * コンテナデータとして取得する
         */
        public function dict($name)
        {
            $c = new ArrayContainer();
            if (!array_key_exists($name, $this->data)) {
                $this->data[$name] = array();
            }
            if (!is_array($this->data[$name])) {
                $this->data[$name] = [$this->data[$name]];
            }
            $c->bind($this->data[$name]);
            return $c;
        }

        public function bind(&$data) 
        {
            $this->data =& $data;
        }

        /**
         * 値を配列で取得する
         */
        public function getArray($name)
        {
            if (!$this->has($name)) {
                return [];
            }
            $data = $this->get($name);
            if (is_array($data)) {
                return $data;
            }
            return [$data];
        }

        // ======================================
        // 配列操作
        // ======================================

        /**
         * 値を追加する
         */
        public function add($name, $value, $prepend = false)
        {
            if ($prepend) {
                return $this->prepend($name, $value);
            }
            return $this->append($name, $value);
        }

        /**
         * 値を先頭に追加する
         */
        public function prepend($name, $value)
        {
            $data = $this->toArray($name);
            array_unshift($data, $value);
            $this->set($name, $data);
            return $this;
        }

        /**
         * 値を末尾に追加する
         */
        public function append($name, $value)
        {
            $data = $this->toArray($name);
            array_push($data, $value);
            $this->set($name, $data);
            return $this;
        }

        /**
         * 値を末尾から取得
         */
        public function pop($name = null)
        {
            if ($name == null) {
                return array_pop($this->data);
            }

            $data = $this->toArray($name);
            if (empty($data)) return false;
            $poped_value = array_pop($data);
            $this->set($name, $data);
            return $poped_value;
        }

        /**
         * 値を先頭から取得
         */
        public function shift($name = null)
        {
            if ($name == null) {
                return array_shift($this->data);
            }
            $data = $this->toArray($name);
            if (empty($data)) return false;
            $shifted_value = array_shift($data);
            $this->set($name, $data);
            return $shifted_value;
        }

        /**
         * 一番先頭の要素を取得
         */
        public function current($name = null)
        {
            if ($name == null) {
                return current($this->data);
            }
            return current($this->data[$name]);
        }

        /**
         * implode
         */
        public function implode($sep, $name = null)
        {
            if ($name == null) {
                return implode($this->data, $sep);
            }
            return implode($this->data[$name], $sep);
        }

        // ======================================
        // ArrayAccess
        // ======================================

        /**
         * Offset Get
         */
        public function offsetGet($name)
        {
            return $this->get($name);
        }

        /**
         * Offset Set
         */
        public function offsetSet($name, $value)
        {
            return $this->set($name, $value);
        }

        /**
         * Offset UnSet
         */
        public function offsetUnset($name)
        {
            unset ($this->data);
        }

        /**
         * Offset Exists
         */
        public function offsetExists($name)
        {
            return $this->has($name);
        }

        // ======================================
        // Iterator
        // ======================================

        /**
         * rewind
         */
        public function rewind ( )
        {
            rewind($this->data);
        }
        /**
         * next
         */
        public function next ( )
        {
            return next($this->data);
        }

        public function key ( )
        {
            return key($this->data);
        }
        /**
         * valid
         */
        public function valid ( )
        {
            return current($this->data);
        }
    }
