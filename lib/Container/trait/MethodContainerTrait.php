<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Container;

trait MethodContainerTrait
    {
        private $methods = [];

        /**
         * メソッドを設定する
         *
         * @param string
         * @param callable
         * @return $this
         */
        public function setMethod ($name, $callback = null)
        {
            if (is_array($name)) {
                foreach ($name as $k => $v) {
                    $this->setMethod($k, $v);
                }
                return $this;
            }
            if (is_string($callback)) {
                $callback = [$this, $callback];
            }
            $this->methods[$name] = $callback;
            return $this;
        }

        /**
         * メソッドを取得する
         *
         * @param string
         * @param callable
         */
        public function getMethod ($name)
        {
            return $this->methods[$name];
        }

        /**
         * メソッド配列を取得する
         *
         * @param string
         * @param callable
         */
        public function getMethodsArray( )
        {
            return $this->methods;
        }


        /**
         * メソッドが定義されているか
         *
         * @param strign
         * @return bool
         */
        public function hasMethod ($name)
        {
            return isset($this->methods[$name]);
        }

        /**
         * メソッドを呼び出す
         *
         * @param strign
         * @return mixed
         */
        public function callMethod ($name)
        {
            $params = func_get_args( );
            $params = array_slice($params, 1);
            return $this->callMethodArray($name, $params);
        }

        /**
         * メソッドを呼び出す
         *
         * @param strign
         * @param array
         * @return mixed
         */
        public function callMethodArray ($name, $params)
        {
            return call_user_func_array($this->getMethod($name), $params);
        }
    }
