<?php
namespace Seaf\Environment;

use Seaf\Core\Pattern\DI;
use Seaf\Kernel\Kernel;

/**
 * 環境クラス用のコンポーネントマネージャ
 * =============================
 *
 * 役割
 * -----------------------------
 * 1. 環境クラスにインスタンスを提供する
 * 2. グローバルに定義されたコンポーネントマネージャを参照する
 */
class ComponentManager extends DI\InstanceManager
{
    private $namespaces = array();

    /**
     * @var Environment
     */
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
        $global = self::getGlobal();
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

    /**
     * グローバル
     */
    public static function getGlobal( )
    {
        if (!(Kernel::registry()->component_manager instanceof self)) {
            Kernel::registry()->component_manager = new self();
        }
        return Kernel::registry()->component_manager;
    }
}
