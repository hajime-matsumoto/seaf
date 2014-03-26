<?php
namespace Seaf\DI;

use Seaf\Pattern;
use Seaf\Data;

/**
 * DIコンテナ
 */
class Factory extends Data\Container\ArrayContainer
{
    use Pattern\Factory;
    use Pattern\Event;

    /**
     * オートロード
     * @var array
     */
    protected $autoloads = array();

    /**
     * ファクトリコンフィグ
     * @var array
     */
    protected $factory_configs = array();

    public function __construct( )
    {
        $this->factory_configs = new Data\Container\ArrayContainer();
    }

    /**
     * Register
     *
     * @param string
     * @param string
     */
    public function register ($name, $definition, $options = null, $callback = null)
    {
        $name = ucfirst($name);

        $this->set(
            $name,
            Definition::factory(
                compact('definition','options','callback')
            )
        );
    }

    /**
     * ファクトリコンフィグをセットする
     */
    public function setFactoryConfigs($configs)
    {
        $c = new Data\Container\ArrayContainer($configs);

        foreach ($c('definitions', array()) as $k=>$v)
        {
            $this->register($k, $v['definition']);
        }

        foreach ($c('configs', array()) as $k=>$v)
        {
            $this->setFactoryConfig($k, $v);
        }
    }

    /**
     * ファクトリコンフィグをセットする
     */
    public function setFactoryConfig($name, $config)
    {
        $name = ucfirst($name);

        $this->factory_configs[$name] = $config;
    }

    /**
     * オートロードクラスを取得する
     */
    public function getClass($name)
    {
        $name = ucfirst($name);

        // AutoLoading
        foreach ($this->autoloads as $autoload) {
            $class = sprintf('%s%s%s',
                $autoload['prefix'],
                ucfirst($name),
                $autoload['suffix']
            );
            if (class_exists($class)) {
                return $class;
            }
        };

        return false;
    }


    /**
     * Hasをオーバライドする
     */
    public function has ($name)
    {
        $name = ucfirst($name);

        if (parent::has($name)) return true;

        // AutoLoading
        if ($class = $this->getClass($name)) {
            if (is_callable($method = $class.'::factory')) {
                $this->register($name, $method, array(
                    $this->factory_configs->get($name, array())
                ));
            } else {
                $this->register($name, $class);
            }
            return true;
        }
        return false;
    }


    /**
     * オートロード
     */
    public function configAutoloads ($autoloads)
    {
        foreach ($autoloads as $autoload) {
            call_user_func_array(
                array($this, 'configAutoload'),
                $autoload
            );
        }
    }

    /**
     * オートロード
     */
    public function configAutoload ($prefix, $suffix = null)
    {
        array_unshift($this->autoloads, compact('prefix','suffix'));
    }
}
