<?php
namespace Seaf\Kernel;

/**
 * オートローダを読み込む
 */
require_once __DIR__.'/AutoLoader.php';

/**
 * カーネルクラス
 * =============================
 *
 * 役割
 * -----------------------------
 * 1. オートローダを設定する
 * 2. カーネルモジュールを読み込む
 */
class Kernel
{
    /**
     * @var Kernel
     */
    private static $instance;

    /**
     * ロードしたモジュールを保存する
     * @var array
     */
    private $modules = array();

    /**
     * シングルトンオブジェクトを取得する
     */
    public static function init ($config = array())
    {
        return self::singleton();
    }
    public static function singleton()
    {
        return self::$instance ? self::$instance: self::$instance = new Kernel();
    }

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        // カーネルを初期化する
        $this->initKernel();
    }

    /**
     * カーネルの初期化用メソッド
     */
    protected function initKernel ( )
    {
        $this->modules = array();

        // オートローダの登録
        $this->modules['AutoLoader'] = AutoLoader::factory(array(
            'namespaces' => array(
                'Seaf' => realpath(__DIR__.'/../')
            )
        ))->register();
    }


    /**
     * スタティックな呼び出しをシングルトンの呼び出しに紐づける
     *
     * @param string
     * @param array
     * @return mixed
     */
    public static function __callStatic ($name, $params)
    {
        return self::singleton()->__call($name, $params);
    }

    /**
     * 呼び出しはカーネルモジュールの呼び出しと見なす
     * モジュールが__invokeを継承している場合はよびだす
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call ($name, $params)
    {
        $name = ucfirst($name);
        if (isset($this->modules[$name])) {
            $module =  $this->modules[$name];
        } else {
            $class =  new \ReflectionClass(__NAMESPACE__.'\\Module\\'.ucfirst($name));
            $module = $this->modules[$name] = $class->newInstance($this);
        }

        if (is_callable($module)) {
            return call_user_func_array($module, $params);
        }
        return $module;
    }
}
