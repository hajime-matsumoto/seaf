<?php
namespace Seaf\DI;

use Seaf\Helper;
use Seaf\Exception;

class Container
{
    /**
     * @var Container
     */
    private static $singleton;

    /**
     * @var array
     */
    protected $instances;

    /**
     * @var Container
     */
    protected $parent = null;

    /**
     * @var mixed
     */
    protected $owner = null;

    /**
     * DI::create時に使用するファクトリ
     * @var Factory
     */
    public $factory;

    /**
     * コンストラクタ
     */
    public  function __construct ()
    {
        $this->instances = new Helper\ArrayHelper();
        $this->factory = new Factory();
    }

    /**
     * 登録
     */
    public function register ($name, $definition, $opts = array(), $callback = null)
    {
        $name = ucfirst($name);
        
        if (is_array($definition) && !is_callable($definition)) {
            $d = Helper\ArrayHelper::factory($definition);
            list($def, $opts, $cb) = $d->export('definition', 'opts', 'callback');
            $this->register($name, $def, $opts, $cb);
        }

        if (is_object($definition)) {
            $this->instances[$name] = $definition;
        } else {
            $this->factory->register($name, $definition, $opts, $callback);
        }
    }

    /**
     * 登録されたコンポーネントを取得する
     * ただし、コンポーネントが->helperを実装していれば呼び出す
     *
     * @param string
     * @param array
     */
    public function call($name, $params)
    {
        $instance = $this->get($name);

        if (method_exists($instance, 'helper')) {
            return call_user_func_array(array($instance, 'helper'), $params);
        }
        return $instance;
    }

    /**
     * 登録されたコンポーネントを取得する
     *
     * @param string
     * @return object
     */
    public function get ($name)
    {
        $name = ucfirst($name);

        // 実体から探す
        if ($this->instances->has($name)) {
            return $this->instances->get($name);
        }

        // ファクトリから探す
        if ($this->factory->has($name)) {
            $instance = $this->create($name);
            $this->instances->set($name, $instance);
            return $instance;
        }

        // 親から探す
        if (!empty($this->parent) && $this->parent->has($name)) {
            return $this->parent->get($name);
        }

        // グローバルから探す
        if ($this != static::singleton() && static::singleton()->has($name)) {
            return static::singleton()->get($name);
        }

        throw new Exception\Exception(array(
            '%sは登録されていません。Class:%s Owner:%s AutoLoad:%s',
            $name,
            get_class($this),
            get_class($this->owner),
            print_r($this->factory->autoload_list, true)
        ));
    }

    /**
     * コンポーネントが存在すればTrue
     *
     * @param string
     * @return true
     */
    public function has ($name)
    {
        $name = ucfirst($name);

        if ($this->instances->has($name)) {
            return true;
        }
        if ($this->factory->has($name)) {
            return true;
        }

        if ($this->parent instanceof self && $this->parent->has($name)) {
            return true;
        }

        // グローバルから探す
        if ($this != static::singleton() && static::singleton()->has($name)) {
            return true;
        }

        return false;
    }

    /**
     * 作成
     */
    public function create ($name)
    {
        $name = ucfirst($name);

        if (!$this->factory->has($name)) {
            throw new Exception\Exception(array(
                '%sは登録されていません',
                $name
            ));
        }

        $instance = $this->factory->get($name)->create();
        return $instance;
    }

    // ------------------------------------------
    // Static
    // ------------------------------------------

    /**
     * Factory
     *
     * @param array 設定
     */
    public static function factory ($config)
    {
        $c = Helper\ArrayHelper::factory($config);

        $class = static::who();
        $di = new $class();

        foreach ($c('components', array()) as $k => $v) 
        {
            // DIにコンポーネントを登録する
            $di->register($k, $v);
        }

        // DIにオートロードプレフィックスとサフィックスを追加する
        if ($c('autoload', false)) {
            $di->factory->addAutoLoad(
                $c('autoload.prefix'),
                $c('autoload.suffix')
            );
        };

        if ($c->parent instanceof self) {
            $di->parent = $c->parent;
        };

        if ($c('owner', false)) {
            $di->owner = $c->owner;
        }
        if ($c('factory', false)) {
            $di->factory = $c->factory;
        }
        return $di;
    }

    /**
     * Singleton
     *
     * @return Container
     */
    public static function singleton ( )
    {
        return (self::$singleton) ? self::$singleton: self::$singleton = new self();
    }

    /**
     * 遅延静的束縛に使用
     */
    public static function who ( )
    {
        return __CLASS__;
    }

}
