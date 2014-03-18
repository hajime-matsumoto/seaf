<?php
namespace Seaf\Kernel;

/**
 * カーネルクラス
 */
class Kernel
{
    /**
     * @var Kernel
     */
    private static $instance;

    /**
     * デバッグフラグ
     * @var bool
     */
    private static $is_debug = false;

    /**
     * 環境モード {development|production}
     * @var string
     */
    public static $envname = 'development';

    /**
     * @var DI\Di
     */
    private $di;

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
        // -----------------------------
        // AutoLoaderを登録
        // -----------------------------
        $loader = Component\AutoLoader::factory(array(
            'namespaces' => array(
                'Seaf' => SEAF_CLASSES_PATH
            )
        ))->register();

        // -----------------------------
        // DIを作成
        // -----------------------------
        $this->di = $di = DI\Container::factory(
            array(
                'name' => 'Kernel::DI',
                'owner' => $this,
                'components'=>array(
                    'AutoLoader' => array(
                        'definition' => $loader
                    )
                ),
                'autoload'=>array(
                    'prefix' => __NAMESPACE__.'\\Component\\',
                    'suffix' => ''
                )
            )
        );
    }

    // ---------------------------------------------------
    // スタティックな処理
    // ---------------------------------------------------

    /**
     * シングルトンオブジェクトを取得する
     */
    public static function singleton()
    {
        return self::$instance ? self::$instance: self::$instance = new self();
    }

    /**
     * 初期化する
     */
    public static function init ($force = false, $debug = false)
    {
        static $initFlg = false;

        if ($initFlg == true && $force != true) return self::singleton();

        $initFlg = true;
        return self::singleton()->initKernel();
    }

    /**
     * シングルトンオブジェクトを破壊する
     */
    public static function destroy()
    {
        if (isset(self::$instance)) self::$instance = null;
    }

    /**
     * デバッグフラグの操作と取得
     */
    public static function isDebug($bool = null)
    {
        if ($bool == null) {
            return self::$is_debug;
        }
        self::$is_debug = $bool;
    }

    /**
     * DIを取得
     * 引数を与えられたらコンポーネントの取得を試みる
     */
    public static function DI ($name = null)
    {
        if ($name == null) {
            return self::singleton()->di;
        }
        return self::singleton()->di->get($name);
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
     * モジュールがhelperメソッドを実装している場合はよび出す
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function __call ($name, $params)
    {
        $instance = $this->di->get($name);

        if (method_exists($instance, 'helper')) {
            return call_user_func_array(array($instance, 'helper'), $params);
        }
        return $instance;
    }

}
