<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DI;

use Seaf\Container\ArrayContainer;
use Seaf\Base;

class Container extends ArrayContainer
{
    use Base\EventTrait;

    /**
     * @var array[Factory,,,]
     */
    private $factories = [];

    /**
     * @var array
     */
    private $cfg = [];

    /**
     * @var Factory
     */
    private $runtimeFuctory = [];

    /**
     *
     */
    public function __construct ( )
    {
        parent::__construct ( );

        $this->addFactory($this->runtimeFuctory = new Factory\RuntimeFactory());
    }

    /**
     * 設定を読み込む
     *
     * @param array
     */
    public function loadConfig ($cfg = [])
    {
        $this->cfg = $cfg;
        if (is_array($cfg)) foreach ($cfg as $k=>$v) {
            //コンフィグが変更されたオブジェクトをリセットする
            $this->del($k);
        }
    }

    /**
     * インスタンスを登録する
     *
     * @param string
     * @param object
     */
    public function register ($name, $instance)
    {
        $name = ucfirst($name);
        if (is_object($instance) && !($instance instanceof \Closure)) {
            $this->set($name, $instance);
        }else{
            $this->runtimeFuctory->register($name, $instance);
        }
    }

    /**
     * インスタンスを取得する
     *
     * @param string
     * @return object
     */
    public function get ($name)
    {
        $name = ucfirst($name);

        if (parent::has($name)) {
            return parent::get($name);
        }

        $object = $this->create($name);
        $this->set($name, $object);
        return $object;
    }

    /**
     * インスタンスを作成する
     *
     * @param string
     * @return object
     */
    public function create ($name)
    {
        $instance = false;
        foreach ($this->factories as $factory) {
            if ($factory->has($name)) {

                // 設定があれば読み込む
                $instance = $factory->create($name, isset($this->cfg[$name]) ? $this->cfg[$name]: []);
            }
        }

        if ($instance === false && class_exists($name)) {
            $instance = new $name( );
        }

        if ($instance == false) {
            throw new Exception\InvalidInstanceName(["%sは登録されていません", $name]);
        }

        $this->trigger('create', ['instance'=>$instance]);
        return $instance;
    }

    /**
     * ファクトリを追加する
     */
    public function addFactory (Factory $factory)
    {
        array_push($this->factories, $factory);
        return $this;
    }
}
