<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Container;

/**
 * 
 */
use Seaf\Util\Util;
use Seaf\Base\Factory;
use Seaf\Base\Event;

/**
 * インスタンスコンテナ
 */
trait InstanceContainerTrait
    {
        use ArrayContainerTrait;

        private $owner;
        private $classes;
        private $aliases;

        public function register($class, $args = [], $options = [])
        {
            $this->classes[$class] = [
                'class'   => $class,
                'args'    => $args,
                'options' => $options
            ];

            $opt = Util::ArrayContainer($options);
            if ($opt->has('alias')) {
                $this->aliases[$opt->get('alias')] = $class;
            }
        }

        /**
         * オーナーをセットする
         *
         * @param object
         */
        public function setOwner($owner)
        {
            $this->owner = $owner;
        }

        /**
         * オーナーを取得する
         *
         * @param object
         */
        public function getOwner( )
        {
            if (empty($this->owner)) {
                return $this;
            }
            return $this->owner;
        }


        /**
         * ファクトリを追加する
         *
         * @param Factory\FactoryIF
         */
        public function addFactory(Factory\FactoryIF $factory)
        {
            $this->factories[] = $factory;
        }

        /**
         * インスタンスを登録する
         *
         * @param string
         * @param object
         */
        public function setInstance($name, $instance)
        {
            $this->setVar($name, $instance);
            return $this;
        }

        /**
         * インスタンスを取得する
         *
         * @param Factory\FactoryIF
         */
        public function getInstance($name)
        {
            if (func_num_args() > 1) {
                return $this->getInstanceArgs($name, array_slice(func_get_args(),1));
            }
            return $this->getInstanceArgs($name, []);
        }

        /**
         * インスタンスを取得する(実体)
         *
         * @param Factory\FactoryIF
         * @param array
         */
        public function getInstanceArgs($name, array $args)
        {
            $name = $this->getRealName($name);
            /*
            if ($this->hasInstance($name, false)) {
                return $this->getVar($name);
            }

            if (!$this->hasInstance($name, true)) {
                throw new Exception\InstanceNotDefined($name, $this);
            }

            $instance = $this->newInstanceArgs($name, $args);
            $this->setInstance($name, $instance);
            return $instance;
             */
            if ($this->has($name)) {
                return $this->get($name);
            }
            $instance = $this->newInstanceArgs($name, $args);
            $this->set($name, $instance);
            return $instance;
        }
        private function getRealName($name)
        {
            return isset($this->aliases[$name]) ?
                $this->aliases[$name]:
                $name;
        }

        /**
         * インスタンスを作成する(実体)
         *
         * @param Factory\FactoryIF
         * @param array
         */
        public function newInstance($name)
        {
            if (func_num_args() > 1) {
                return $this->newInstanceArgs($name, array_slice(func_get_args(), 1));
            }
            return $this->newInstanceArgs($name, []);
        }

        /**
         * インスタンスを作成する(実体)
         *
         * @param Factory\FactoryIF
         * @param array
         */
        public function newInstanceArgs($name, array $args)
        {
            if (isset($this->aliases[$name])) {
                $name = $this->aliases[$name];
            }
            if ($this->classes[$name]) {
                $info = $this->classes[$name];
                if (empty($args)) {
                    $args = $info['args'];
                }
                $class = $info['class'];

                // クラス名からインスタンスを作成
                return Util::ClassName($class)->newInstanceArgs($args);
            }
            return;
            $this->mapFactories(function($factory) use ($name, $args, &$instance) {
                if ($factory->canCreate($name)) {
                    $instance = $factory->newInstanceArgs($name, $args);
                    return $continue = false;
                }
            });

            if (!$instance) {
                throw new Exception\InstanceCantCreate($name, $this);
            }

            if (!$this->owner) $this->owner = $this;

            if (
                $instance instanceof Event\ObservableIF
                &&
                $this->owner instanceof Event\ObserverIF
            ) {
                $instance->acceptEventObserver($this->owner);
            }


            return $instance;
        }

        /**
         * インスタンスが存在するか？
         *
         * @param string
         * @param bool $useFactory=true ファクトリも検索対象にする
         * @param Factory\Factory 見つかったファクトリを返却する
         */
        public function hasInstance($name, $useFactory = true, &$factory = null)
        {
            return true;
            if ($this->hasVar($name)) {
                return true;
            }

            if ($useFactory == false) return false;

            $this->mapFactories(function($factory) use ($name, &$result) {
                if ($factory->canCreate($name)) {
                    $result = $factory;
                    return $continue = false;
                }
            });

            if (!$result) return false;

            $factory = $result;
            return true;
        }

        /**
         * ファクトリをマップする
         */
        public function mapFactories($callback) 
        {
            foreach ($this->factories as $factory) 
            {
                if (false === $continue = $callback($factory)) 
                {
                    break;
                }
            }
        }
    }
