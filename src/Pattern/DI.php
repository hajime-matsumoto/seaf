<?php
namespace Seaf\Pattern;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;
use Seaf\Data\Container;
use Seaf\Pattern\Factory;

abstract class DI {

    /**
     * @var array
     */
    private $instances;

    /**
     * @var array
     */
    private $factory;

    /**
     * @var array
     */
    protected $tryed;

    /**
     * オートロードするコンポーネントのネームスペース
     * @var array
     */
    public $ns_list = array();

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->initDI();
    }

    /**
     * DIを初期化する
     */
    protected function initDI()
    {
        $this->instances = new Container\Base();
        $this->factory = new Factory();
    }

    /**
     * コンポーネントを登録する
     *
     * @param string
     * @param mixed
     * @param array
     * @param mixed
     */
    public function register ($name, $definition, $options = array(), $callback = null)
    {
        if (is_object($definition)) {
            $this->instances->set($name, $definition);
        } else {
            $this->factory->register($name, $definition, $options, $callback);
        }
    }

    /**
     * コンポーネントの存在を確認
     *
     * @param string
     * @return bool
     */
    public function has ($name)
    {
        if ($this->instances->has($name)) {
            return true;
        }

        if ($this->factory->has($name)) {
            return true;
        }

        // リストから探す
        $this->tryed = array();
        foreach ($this->ns_list as $ns) {
            $this->tryed[] = $class = $ns.'\\'.ucfirst($name);
            if (class_exists($class)) { // 見つかったらDIに登録しておく
                $this->register($name, $class);
                return true;
            }
        }


        return false;
    }

    /**
     * コンポーネントを取得する
     *
     * @param string
     * @return object
     */
    public function get ($name)
    {
        if (!$this->has($name)) {
            throw new Exception(array("%sは登録されていません",$name));
        }

        if ($this->instances->has($name)) {
            $instance =  $this->instances->get($name);
            return $instance;
        }
        $this->instances->set($name, $instance = $this->create($name));
        return $instance;
    }


    /**
     * コンポーネントを作成する
     *
     * @param string
     * @return object
     */
    public function create($name)
    {
        return $this->factory->get($name)->create();
    }

    /**
     * コール
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call($name, $params)
    {
        return $this->call($name, $params);
    }

    /**
     * コール
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function call($name, $params)
    {
        if ($this->has($name)) {
            $instance = $this->get($name);
            if (is_callable($instance)) {
                Kernel::logger('Kernel::DI')->debug(array("%sはcallable",$name));
                return Kernel::dispatcher($instance, $params, $this)->dispatch();
            }
            return $instance;
        }
        return $this->DICallFallBack($name, $params);
    }

    /**
     * 自動読み込みするクラスを定義する
     *
     * @param string
     * @param string
     * @param bool
     * @return void
     */
    public function addComponentNamespace ($class, $prefix = '', $prepend = true)
    {
        $class = Kernel::ReflectionClass($class);
        $ns = $class->getNamespaceName().$prefix;

        if ($prepend) {
            array_unshift($this->ns_list,$ns);
        } else {
            array_push($this->ns_list,$ns);
        }
    }

    abstract public function DICallFallBack($name, $params);
}
