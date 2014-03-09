<?php
/**
 * 環境
 */
namespace Seaf\Environment;

use Seaf\DI\HelperManager;
use Seaf\DI\ComponentManager;
use Seaf\Log\Log;
use Seaf\Helper\ArrayHelper;
use Seaf\Commander\Command;

/**
 * 環境クラス
 * ==================================
 *
 * プログラムの実行環境を整えるクラス
 *
 */
class Environment {

    /**
     * 値の格納用
     * @var array
     */
    private $vars = array();

    /**
     * @var HelperManager
     */
    private $helperManager = array();

    /**
     * @var ComponentManager
     */
    private $componentManager = array();

    /**
     * インスタンスを作成
     */
    public function __construct ( ) 
    {
        // ヘルパマネージャ
        $this->helperManager   = new HelperManager($this);

        // コンポーネントマネージャ
        $this->componentManager   = new ComponentManager($this);

        // 初期化処理を呼び出す
        $this->init();
    }

    /**
     * 初期化処理
     * -----------------------
     * * メソッドを組み込む
     * *  
     */
    public function init ( ) 
    {

        // ロガーを組み込む
        $this->bind('Seaf\Log\Log',array(
            'emerg'    => 'emerg',
            'alert'    => 'alert',
            'critical' => 'critical',
            'error'    => 'error',
            'warn'     => 'warn',
            'info'     => 'info',
            'debug'    => 'debug'
        ));

        // DIを組み込む
        $this->bind($this->componentManager,array(
            'register' => 'register'
        ));

        // 変数操作を組み込む
        $this->bind($this, array(
            'set' => '_set',
            'get' => '_get',
            'push' => '_push',
            'getVars' => '_getVars'
        ));

        // イベントを組み込む
        $this->bind('event',array(
            'on' => 'on',
            'off'=>'off',
            'trigger'=>'trigger'
        ));

        // コンフィグを組み込む
        $this->bind('config',array(
            'setConfig' => 'set',
            'getConfig'=> 'get',
            'loadConfig'=>'load'
        ));

        // Viewを組み込む
        $this->register('view', 'Seaf\View\View', array(), function ($view) {

            // レジストリの値を合わせる
            $view->vars += $this->vars;

            // コンフィグオブジェクトを共有させる
            $view->register('config', function(){
                return $this->config();
            });

            // Viewのパスを追加する
            $view->addPath(
                realpath(implode('/', $this->getConfig(array('root.path','view.path'))))
            );
        });

    }


    /** 変数操作系のメソッド **/

    public function _set ($name, $value) 
    {
        ArrayHelper::set($this->vars,$name,$value);
    }
    public function _get ($name, $default = null) 
    {
        return ArrayHelper::get($this->vars,$name,$default);
    }
    public function _push ($name, $value)
    {
        return ArrayHelper::push($this->vars,$name,$value);
    }
    public function _getVars()
    {
        return $this->vars;
    }


    /**
     * メソッドを呼び出す
     * ===========================
     *
     * 処理順序
     * --------------------------
     * 1. ヘルパに登録されているか探す
     * 2. インスタンスが登録されているか探す
     */
    public function call ($name, $params) {

        // ヘルパーに登録されていれば
        // ヘルパーに処理を委譲する
        if ($this->helperManager->isCallable($name)) {
            return $this->helperManager->call($name, $params);
        }

        // コンポーネントが登録されていれば
        // コンポーネントを取得する
        if ($this->componentManager->has($name)) {
            return Command::invoke(
                array($this->componentManager,'get'),
                $name, $params
            );
        }

        throw new \Exception("Invalid Call ".$name);
    }

    /**
     * メソッドを呼び出す(マジックメソッド)
     * ===========================
     * ::call へフォワード
     */
    public function __call ($name, $params) {
        $return =  $this->call($name, $params);
        return $return;
    }

}
