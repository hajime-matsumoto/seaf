<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

trait ComponentCompositeTrait
    {
        private $componentContainer;

        /**
         * コンポーネントコンテナを登録する
         *
         * @param string|object
         */
        public function setComponentContainer ($container)
        {
            $this->componentContainer = $container;
        }

        /**
         * コンポーネントを登録する
         *
         * @param string
         * @param object
         */
        public function registerComponent ($name, $component)
        {
            $this->component( )->register($name, $component);
        }

        /**
         * コンポーネントを取得する
         * 引数無でコンポーネントコンテナを返却する
         */
        public function component ($name = null)
        {
            if ($name == null) {
                if (is_object($this->componentContainer)) {
                    return $this->componentContainer;
                } else {
                    $class = $this->componentContainer;
                    return $this->componentContainer = new $class($this);
                }
            } else {
                return $this->component( )->get($name);
            }
        }

        /**
         * コンポーネント呼び出し
         */
        public function componentCall ($name, $args)
        {
            $comp = $this->component($name);
            if (method_exists($comp, $method = 'componentHelper')) {
                return call_user_func_array([$comp, $method], $args);
            }
            return $comp;
        }

        /**
         * コンポーネントをリセットする 
         */
        public function componentReset ($names)
        {
            if (!is_array($names)) $names = [$names];
            foreach($names as $name) {
                $this->component()->del(ucfirst($name));
            }
        }
    }
