<?php
namespace Seaf\Core\DI;

use Exception;
use Seaf\Core\Kernel;

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
     *          "options" => array(),
     *          "callback" => <callback>
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
     *
     * @param string $alias
     * @param array $config [定義,引数,コールバック]
     */
    public function registerDefinition ($alias, $config)
    {
        // 定義が正しくなければエラー
        $isValid = 
            isset($config['definition']) && 
            (
                is_callable($config['definition']) || 
                (is_string($config['definition']) && class_exists($config['definition']))
            );
        if (!$isValid) {
            throw new Exception("DefinitionInvalid ".$alias);
        }

        $definition = $config['definition'];
        $options    = isset($config['options']) ? $config['options']: array();
        $callback   = isset($config['callback']) ? $config['callback']: null;

        // 定義のタイプを取得
        $type       = is_string($definition) && class_exists($definition) ? self::TYPE_CLASS_NAME: self::TYPE_CALLBACK;

        $this->definitions[$alias] = array($type, $definition, $options, $callback);
    }

    /**
     * 定義を持っているか
     *
     * @param string
     * @return bool
     */
    public function hasDefinition ($alias) {
        return array_key_exists($alias, $this->definitions);
    }

    /**
     * 作成する
     *
     * @param string
     */
    public function create ($alias) {
        if (!$this->hasDefinition($alias)) {
            throw new Exception($alias." Dose not Exists");
        }

        list($type,$definition,$options,$callback) = $this->definitions[$alias];

        if ($type == self::TYPE_CLASS_NAME) {
            $instance = Kernel::newInstanceArgs($definition, $options);
        }else{
            $instance =  Kernel::invokeArgs($definition, $options);
        }

        if (is_callable($callback)) {
            call_user_func($callback,$instance);
        }
        return $instance;
    }
}
