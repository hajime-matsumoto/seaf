<?php
namespace Seaf\Kernel;

/**
 * カーネルクラス
 * =============================
 *
 * 役割
 * -----------------------------
 * 1. 生のシステムとなるべくかかわら出ない
 * 2. カーネルモジュールの組み換え
 *
 * モジュールの有効化
 * -----------------------------
 * Module\System::register($Kernel)
 *
 * 特殊なメソッド
 * -----------------------------
 * Kernel::invokeArgs($callback, $params)   # コールバックの実行
 * Kernel::newInstanceArgs($class, $params) # クラスのインスタンス作成
 * Kernel::ReflectionClass($class) # クラスのインスタンス作成
 *
 * 仕事の優先順位
 * -----------------------------
 * 1. オートローダを設定する
 */
class Kernel
{
    private static $instance;

    /**
     * ロードしたモジュールを保存する
     */
    private $modules = array();

    public static function init ($config = array())
    {
        return self::singleton()->_init($config);
    }

    public static function singleton()
    {
        return self::$instance ? self::$instance: self::$instance = new Kernel();
    }

    public function _init ($config)
    {
        static $initialized = false;
        if ($initialized == true) return $this;

        // このファイルの設定位置は
        // src/Kernel/Kenel.phpのはずなので
        // __DIR__.'/../'がSeafのルートになるはず
        $this->loader = AutoLoader::init(array(
            'namespaces' => array(
                'Seaf' => realpath(__DIR__.'/../')
            )
        ));
        $initialized = true;
        return $this;
    }

    public static function AutoLoader()
    {
        return self::singleton()->loader;
    }

    public static function __callStatic ($name, $params)
    {
        return self::singleton()->__call($name, $params);
    }

    public function __call ($name, $params)
    {
        if (isset($this->modules[$name])) {
            $module =  $this->modules[$name];
        } else {
            $class =  new \ReflectionClass(__NAMESPACE__.'\\Module\\'.ucfirst($name));
            $module = $this->modules[$name] = $class->newInstanceArgs($params);
        }
        if (!empty($params) && is_callable($module)) {
            return call_user_func_array($module, $params);
        }
        return $module;
    }
}

/**
 * オートローダ
 * ----------------------
 */
class AutoLoader
{
    const NAMESPACE_SEPARATOR    = '\\';
    const DIRECTORY_SEPARATOR    = '/';

    /**
     * ネームスーペースのリスト
     * @var $array
     */
    public $namespaces = array();

    /**
     * ライブラリのリスト
     * @var $array
     */
    public $libraries = array();

    /**
     * init
     *
     * @param $config
     * @return void
     */
    public static function init ($config)
    {
        $loader = new AutoLoader();
        if (isset($config['namespaces'])) foreach ($config['namespaces'] as $ns=>$path) {
            $loader->namespaces[$ns][] = $path;
        }

        if (isset($config['libraries'])) foreach ($config['libraries'] as $path) {
            $loader->libraries[] = $path;
        }

        spl_autoload_register(array($loader,'loadNamespace'));
        spl_autoload_register(array($loader,'library'));
        return $loader;
    }

    /**
     * addNamespace
     *
     * @param $namespace, $path
     * @return void
     */
    public function addLibrary ($path)
    {
        if (is_array($path)) {
            foreach ($path as $v) {
                $this->libraries($v);
            }
            return $this;
        }

        $this->libraries[] = $path;
        return $this;
    }

    /**
     * addNamespace
     *
     * @param $namespace, $path
     * @return void
     */
    public function addNamespace ($namespace, $path)
    {
        if (is_array($namespace)) {
            foreach ($namespace as $k=>$v) {
                $this->addNamespace($k, $v);
            }
            return $this;
        }

        $this->namespaces[$namespace][] = $path;
        return $this;
    }

    /**
     * Namespaceベースの読み込み
     *
     * @param string;
     */
    public function loadNamespace ($class) 
    {
        $list = $this->namespaces;

        foreach ($list as $ns => $paths) {
            foreach ($paths as $path) {
                if (0 === strpos($class,$ns)) {
                    $filename = $this->transClassToFileName(substr($class,strlen($ns)));

                    $file_path = $path.$filename.'.php';
                    if (file_exists($file_path)) {
                        require_once $file_path;
                        return;
                    }
                }
            }
        }
    }

    /**
     * library
     *
     * @param $class
     * @return void
     */
    public function library ($class)
    {
        $filename = $this->transClassToFileName($class);

        foreach ($this->libraries as $path) {
            $file_path = $path.'/'.$filename.".php";
            if (file_exists($file_path)) {
                require_once $file_path;
                return;
            }
        }
    }


    /**
     * Class名をPath名に変換する
     */
    private function transClassToFileName ($class) {
        $ns = self::NAMESPACE_SEPARATOR;
        $ds = self::DIRECTORY_SEPARATOR;

        return str_replace($ns, $ds, $class);
    }
}

