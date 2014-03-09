<?php
/**
 * DI
 */
namespace Seaf\DI;


use Seaf\Commander\Command;

/**
 * ファクトリクラス
 */
class Factory {

    const TYPE_CALLBACK   = 'callback';
    const TYPE_CLASS_NAME = 'classname';

    /**
     * @var array
     */
    private $nsList = array();
    private $definitions = array();

    /**
     * Usage
     * ================================
     *
     * <code>
     *  Factory::factory(array(
     *      "<alias>" => array(
     *          "definition" => <callback or classname>,
     *          "options" => array()
     *      ),
     *  ));
     * </code>
     *
     * @param array config
     */
    public static function factory ($config = array()) {
        $factory = new Factory();

        // 定義を登録する
        if (is_array($config)) foreach($config as $alias => $alias_config) {
            $factory->registerDefinition($alias, $alias_config);
        }

        return $factory;
    }

    /**
     * 登録する
     */
    public function registerDefinition ($alias, $config)
    {
        if (!isset($config['definition'])) {
            throw new Exception\DefinitionInvalid($alias, $config);
        }
        $definition = $config['definition'];
        $options    = isset($config['options']) ? $config['options']: array();

        $isValid = is_callable($definition) || (is_string($definition) && class_exists($definition));

        if (!$isValid) {
            throw new Exception\DefinitionInvalid($alias, $config);
        }

        $type       = is_string($definition) && class_exists($definition) ? self::TYPE_CLASS_NAME: self::TYPE_CALLBACK;

        $this->definitions[$alias] = array($type, $definition, $options);
    }

    public function hasDefinition ($alias) {
        return array_key_exists($alias, $this->definitions);
    }

    /**
     * 作成する
     */
    public function create ($alias) {
        if (!$this->hasDefinition($alias)) {
            throw new Exception\AliasNotFound($alias);
        }

        list($type,$definition,$options) = $this->definitions[$alias];

        if ($type == self::TYPE_CLASS_NAME) {
            return Command::newInstanceArgs($definition, $options);
        }

        return Command::invokeArgs($definition, $options);
    }
}
