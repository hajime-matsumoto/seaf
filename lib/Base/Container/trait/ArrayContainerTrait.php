<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

use Seaf\Util\Util;

/**
 * 配列コンテナ
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
         * コンテナデータとして取得する
         */
        public function dict($name)
        {
            if (!$this->has($name)) {
                $this->set($name, new ArrayContainer());
                return $this->get($name);
            }

            $data = $this->get($name);
            if($data instanceof ContainerIF) {
                return $data;
            }
            $this->set($name, new ArrayContainer());
            return $this->get($name)->set($data);
        }

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

        /**
         * 値を追加する
         */
        public function add($name, $value, $prepend = false)
        {
            $data = $this->get($name, []);
            if(!is_array($data) && !empty($data)) {
                $data = [$data];
            }
            if ($prepend) {
                array_unshift($data, $value);
            }else{
                array_push($data, $value);
            }
            $this->set($name, $data);
            return $this;
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
        public function isEmpty($name)
        {
            $name = $this->nameNormalizer($name);
            return isset($this->data[$name]) && !empty($this->data[$name]) ? false: true;
        }

        /**
         * ノーマルな配列に変換
         *
         * @param string
         * @return bool
         */
        public function toArray ( )
        {
            return $this->data;
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
    }
