<?php
namespace Seaf\Core;

/**
 * 環境クラス用のコンポーネントマネージャ
 * =============================
 *
 * 役割
 * -----------------------------
 * 1. 環境クラスにインスタンスを提供する
 * 2. グローバルに定義されたコンポーネントマネージャを参照する
 */
class ComponentManager extends DI\Container
{
    const GLOBAL_KEY = 'global_component_manager';

    private $namespaces = array();

    private $env;

    /**
     * コンストラクタ
     *
     * @param
     * @return void
     */
    public function __construct (Environment $env = null)
    {
        parent::__construct();

        $this->env = $env;

        // 環境クラスのネームスペース\\Componentを登録する
        do {
            $this->addNamespace($this->getNamespace($env).'\\Component');
        } while ($env = get_parent_class($env));
    }

    /**
     * addNamespace
     *
     * @param $namespace, $suffix = 'Component'
     * @return void
     */
    public function addNamespace ($namespace, $suffix = 'Component')
    {
        $this->namespaces[$namespace] = $suffix;
    }

    /**
     * getNamespace
     *
     * @param $class
     * @return void
     */
    private function getNamespace ($class)
    {
        $class = is_object($class) ? get_class($class): $class;
        return substr($class,0,strrpos($class,'\\'));
    }

    /**
     * このコンポーネントマネージャをグローバルに登録する
     */
    public function globalize ()
    {
        Kernel::registry()->set(self::GLOBAL_KEY, $this);
    }

    /**
     * インスタンスの作成
     */
    protected function newInstance ($alias) {
        $instance = parent::newInstance($alias);

        if (method_exists($instance,'initComponent')) {
            $instance->initComponent($this->env);
        }
        return $instance;
    }

    /**
     * has
     *
     * @param $name
     * @return void
     */
    public function has ($name)
    {
        if (parent::has($name)) return true;

        // 登録されているネームスペースを動的に読み込む
        foreach ($this->namespaces as $ns=>$suffix) {
            $try = $ns.'\\'.ucfirst($name).$suffix;

            if (class_exists($try)) {
                $this->register($name,$try);
                return true;
            }
        }

        // グローバルに登録されているコンポーネントを探す
        $global = Kernel::registry()->get(self::GLOBAL_KEY);
        if (!$global || $global == $this) { // 無限ループの回避
            return false;
        } else {
            if( $global->has($name) ) {
                $this->register($name, $global->get($name));
                return true;
            }
        }
        return false;
    }
}
