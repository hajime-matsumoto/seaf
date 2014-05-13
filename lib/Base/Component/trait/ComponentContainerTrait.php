<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * コンポーネント
 */
namespace Seaf\Base\Component;

use Seaf\Util\Util;
use Seaf\Base\Proxy;

/**
 * コンポーネントコンテナオーナ
 */
trait ComponentContainerTrait
    {
        private $componentContainer;

        /**
         * コンテナの取得
         */
        protected function componentContainer()
        {
            if (!$this->componentContainer) {
                $this->componentContainer = Util::Dictionary([
                    'instance' => [],
                    'define'   => []
                ]);
                $this->componentContainer->caseSensitive(false);

                if (method_exists($this, 'onInitComponentContainer')) {
                    $this->onInitComponentContainer();
                }
            }
            return $this->componentContainer;
        }

        /**
         * コンポーネントを上書く
         */
        public function setComponent($name, $object)
        {
            $this->debug([
                'Component Orver Write >>> %s : %s <<<',
                $name,
                gettype($object)
            ]);
            $this->componentContainer->dict('instance')->set($name, $object);
        }

        /**
         * コンポーネントを上書く
         */
        private function ensureComponent($name, &$object)
        {
            if (!$object) { //オブジェクトが居ない
                $object = $this->loadComponent($name);
                return $object;
            }
            $this->setComponent($name, $object);
            return $object;
        }


        /**
         * コンポーネントの登録
         */
        public function registerComponent($name, $class = null, $args = [])
        {
            if (is_array($name)) {
                foreach ($name as $k=>$v) {
                    $this->registerComponent($k, $v);
                }
                return $this;
            }
            array_unshift($args, $this);
            $this->componentContainer()->dict('define')->set($name, [$class,$args]);

            if (!is_string($class)) {
                $class = gettype($class);
            }
            $this->debug("Component Registered >>> $name = $class <<<");
            return $this;
        }

        /**
         * コンポーネント一覧を表示する
         */
        public function showComponentList( )
        {
            printf('*** Module List >>> %s <<< ***'."\n", $this->getObjectName());
            foreach ($this->componentContainer()->dict('define') as $k=>$v)
            {
                printf('%s %s'."\n", $k, $v[0]);
            }
        }

        /**
         * コンポーネントの存在を確認する
         */
        public function hasComponent($name)
        {
            $ins = $this->componentContainer()->dict('instance');
            $def = $this->componentContainer()->dict('define');

            if ($ins->has($name) || $def->has($name)) {
                return true;
            }
            return false;
        }

        /**
         * コンポーネントを呼び出す
         */
        public function loadComponent($name)
        {
            $ins = $this->componentContainer()->dict('instance');
            $def = $this->componentContainer()->dict('define');

            if ($ins->has($name)) return $ins->get($name);
            if (!$def->has($name)) return false;

            return $this->createComponent($name);
        }

        /**
         * コンポーネントを作る
         */
        protected function createComponent($name)
        {
            $ins = $this->componentContainer()->dict('instance');
            $def = $this->componentContainer()->dict('define');

            list($class, $args) = $def->get($name);

            if (is_string($class)) {
                $component = Util::ClassName($class)->newInstanceArgs($args);
                $component->tag('object_name', $name);
            }elseif(is_callable($class)){
                $component = call_user_func_array($class, $args);
            }

            $this->info(["Component Loaded >>> %s <<<", $name]);

            $ins->set($name, $component);

            return $component;
        }

    }
