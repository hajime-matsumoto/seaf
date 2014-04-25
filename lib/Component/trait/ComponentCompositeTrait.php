<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Component;
use Seaf\Container;

trait ComponentCompositeTrait
    {
        use Container\ArrayContainerTrait;

        abstract public function raiseError($code, $params =[]);

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
                $this->configs[ucfirst($k)] = $v;
            }
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
                if ($instance === false) continue;
            }

            if ($instance == false) {
                $this->raiseError('COMPONENT_NOT_FOUND', [$name]);
            }
            $this->setVar($name, $instance);
            return $instance;
        }

        /**
         * コンポーネントを取得する
         */
        private function getComponentConfig ( $name )
        {
            if (!isset($this->configs[$name])) return [];

            return $this->configs[$name];
        }

    }
