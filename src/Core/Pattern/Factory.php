<?php
namespace Seaf\Core\Pattern;

use Seaf\Core\Exception;
use Seaf\Kernel\Kernel;

/**
 * ファクトリパターンを実装する
 */
class Factory 
{
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
    public static function factory ($config = array()) 
    {
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

        return $this->definitions[$alias] = new FactoryDefinition($definition, $options, $callback);
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

        $def = $this->definitions[$alias];

        if ($def->type == self::TYPE_CLASS_NAME) {
            $instance = Kernel::dispatch()->newInstanceArgs($def->initializer, $def->options);
        }else{
            $instance =  Kernel::dispatch()->invokeArgs($def->initializer, $def->options);
        }

        if (is_callable($def->callback)) {
            call_user_func($def->callback,$instance);
        }
        return $instance;
    }
}

/**
 * Factoryの定義
 */
class FactoryDefinition
{
    /**
     * new の時に渡るパラメタ
     * @var array
     */
    public $options;

    /**
     * instance 生成時のコールバック関数
     * @var mixed
     */
    public $callback;

    /**
     * 生成のイニシャライザ
     * @var mixed
     */
    public $initializer;

    /**
     * 定義種別
     * @var string
     */
    public $type;

    /**
     * __construct
     *
     * @param $options, $callback
     */
    public function __construct ($initializer, $options, $callback)
    {
        // 定義のタイプを取得
        $this->type        = is_string($initializer) && class_exists($initializer) ? Factory::TYPE_CLASS_NAME: Factory::TYPE_CALLBACK;
        $this->initializer = $initializer;
        $this->options     = $options;
        $this->callback    = $callback;
    }

    /**
     * setOpts
     *
     * @param 
     * @return void
     */
    public function setOpts ()
    {
        $this->options = func_get_args();
    }
}
