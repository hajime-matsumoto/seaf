<?php
namespace Seaf\Core;


/**
 * 環境クラス
 * =============================
 *
 * 役割
 * -----------------------------
 * 少し大きめのライブラリの基本機能詰め合わせ。
 *
 * 1. メソッドを動的に定義する
 * 2. インスタンスを管理する
 *
 * 使い方
 * -----------------------------
 * $this->map($name, $command);    # メソッドの定義
 * $this->remap($name, $command);  # メソッドを上書きする
 * $this->isMaped($name);          # メソッドが存在するか
 * $this->register($name,$config); # インスタンスの登録
 *
 * マジックメソッド __call
 * -----------------------------
 * Environmenntに実装されていないメソッドをコール
 * するとhelperManagerからコールバックを受け取ろうとします。
 * マップされていなければ、componentManagerからインスタンスを取得します。
 */
class Environment
{
    /**
     * @var array
     */
    protected $vars;

    /**
     * @var InstanceManager
     */
    protected $cm;

    /**
     * @var InstanceManager
     */
    protected $hm;

    /**
     * コンストラクタ
     *
     * @param
     * @return void
     */
    public function __construct ()
    {
        $this->cm = new ComponentManager($this);
        $this->hm = new HelperManager($this);

        $this->hm->bind($this->hm, array(
            'map'     => 'map',
            'remap'   => 'remap',
            'isMaped' => 'isMaped',
            'bind'    => 'bind'
        ));

        $this->initMethodMap();
        $this->initEnvironment();
    }

    /**
     * メソッドマップを初期化します
     */
    protected function initMethodMap ()
    {
        $this->bind($this->cm, array(
            'register'     => 'register',
            'getComponent' => 'get'
        ));

        // ロガーを組み込む
        $this->bind('log',array(
            'emerg'    => 'emerg',
            'alert'    => 'alert',
            'critical' => 'critical',
            'error'    => 'error',
            'warn'     => 'warn',
            'info'     => 'info',
            'debug'    => 'debug'
        ));

        // カーネルを組み込む
        $this->bind('Seaf\Core\Kernel',array(
            'fs'  => 'fs',
            'sys' => 'system'
        ));

        // 変数操作
        $this->map('set',function($k,$v){
            $this->vars[$k] = $v;
        });
        $this->map('get',function($k, $default = null){
            return isset($this->vars[$k]) ? $this->vars[$k]: $default;
        });
    }


    protected function initEnvironment( )
    {
        $this->set('name', get_class($this));
    }


    /**
     * コール処理
     * ==================================
     *
     * 優先順位
     * ----------------------------------
     * 1. ヘルパーの呼び出し
     * 2. インスタンスの呼び出し
     *
     * @param $name, $params
     * @return void
     */
    public function call ($name, $params)
    {
        if ($this->hm->isMaped($name)) {
            $closure = $this->hm->get($name);
            return Kernel::invokeArgs($closure, $params);
        }
        if ($this->cm->has($name)) {
            return $this->cm->get($name);
        }

        throw new Exception(array("呼び出せないメソッドが呼ばれました。 %s",$name,$this));
    }

    public function __call ($name, $params) 
    {
        return $this->call($name, $params);
    }

    /**
     * グローバル
     */
    public static function getGlobal( )
    {
        if (!Kernel::rg()->get('environment', false)) {
            $env     = new Environment();
            Kernel::rg()->set('environment', $env);
        }
        return Kernel::rg()->get('environment');
    }
}
