<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Component;

use Seaf\Container;

trait ComponentCompositeTrait
    {
        use Container\ArrayContainerTrait;

        abstract public function trigger($name, $params = []);

        /**
         * @var array
         */
        private $loaders = [];

        /**
         * @var array
         */
        private $configs = [];

        /**
         * コンポーネントローダを設定する
         */
        public function addComponentLoader ( Loader\LoaderIF $cl )
        {
            $this->loaders[] = $cl;
        }

        /**
         * コンポーネント設定を読み込む
         */
        public function loadComponentConfig ( $cfg )
        {
            foreach ($cfg as $k=>$v) {
                $this->configs[$k = ucfirst($k)] = $v;
                $this->delComponent($k);
            }
        }

        /**
         * コンポーネントを設定する
         */
        public function setComponent ($name, $component)
        {
            $this->setVar($name, $component);
        }

        /**
         * コンポーネントを削除する
         */
        public function delComponent ($name)
        {
            $this->delVar($name);
        }

        /**
         * コンポーネントを取得する
         */
        public function getComponent ( $name )
        {
            $name = ucfirst($name);

            if ($this->hasVar($name)) {
                return $this->getVar($name);
            }

            foreach ($this->loaders as $loader) {
                $instance = $loader->create($name, [$this->getComponentConfig($name)]);
                if ($instance === false) {
                    continue;
                }
                break;
            }

            if ($instance == false) {
                throw new Exception\ComponentNotFound($name, $this);
            }
            $this->setVar($name, $instance);
            $this->trigger('component.create',[
                'component' => $instance
            ]);
            return $instance;
        }

        /**
         * コンポーネントを取得する
         */
        private function getComponentConfig ( $name )
        {
            if (!isset($this->configs[$name])) return [];

            return new Container\ArrayContainer($this->configs[$name]);
        }

    }
