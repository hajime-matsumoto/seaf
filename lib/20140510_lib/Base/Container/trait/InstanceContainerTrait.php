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

class CouldnotCreateInstance extends \Exception
{
    public function __construct ($name)
    {
        parent::__construct(sprintf(
            'Could Not CreateInstance %s',
            $name
        ));
    }
}

/**
 * インスタンスコンテナ
 */
trait InstanceContainerTrait
    {
        use ArrayContainerTrait;
        private $factory;

        /**
         * コンストラクタ
         */
        public function initInstanceContainer ($owner = null)
        {
            // 大文字小文字の区別をしない
            $this->caceSensitive(false);

            $this->factory = new Factory\Factory();
        }

        public function getFactory( )
        {
            return $this->factory;
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
         * インスタンスをセットする
         */
        public function setInstance($name, $instance)
        {
            $this->set($name, $instance);
        }

        /**
         * インスタンスがあるか
         */
        public function hasInstance($name)
        {
            if ($this->has($name)) return true;
            if ($this->getFactory()->canCreate($name)) return true;
            return false;
        }


        /**
         * インスタンスを取得する(実体)
         *
         * @param Factory\FactoryIF
         * @param array
         */
        public function getInstanceArgs($name, array $args = [])
        {
            if ($this->has($name)) {
                return $this->get($name);
            }
            $instance = $this->newInstanceArgs($name, $args);
            $this->set($name, $instance);
            return $instance;
        }

        public function newInstance($name)
        {
            $args = func_num_args() > 1 ? 
                array_slice(func_get_args(),1):
                [];
            return $this->newInstanceArgs($name, $args);
        }

        public function newInstanceArgs($name, array $args = [])
        {
            $this->fireEvent('before.newInstanceArgs', [
                'name' => &$name,
                'args' => &$args
            ]);

            $instance = $this->factory->newInstanceArgs($name, $args);

            if (!$instance)  {
                throw new CouldnotCreateInstance($name);
            }


            $this->fireEvent('after.newInstanceArgs', [
                'name' => &$name,
                'args' => &$args,
                'instance' => $instance
            ]);

            $this->fireEvent('create', [
                'name' => &$name,
                'args' => &$args,
                'instance' => $instance
            ]);
            return $instance;
        }
    }
