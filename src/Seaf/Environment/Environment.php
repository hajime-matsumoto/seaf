<?php
/**
 * 環境
 */
namespace Seaf\Environment;

use Seaf\DI\InstanceManager;
use Seaf\DI\HelperManager;
use Seaf\DI\ComponentManager;

use Seaf\Log\Log;
use Seaf\Helper\ArrayHelper;
use Seaf\Commander\Command;

/**
 * 環境クラス
 */
class Environment {

    private $vars = array();

    /**
     * @var InstanceManager
     */
    private $instanceManager = array();


    /**
     *
     */
    public function __construct ( ) {
        // ヘルパマネージャ
        $this->helperManager   = new HelperManager($this);

        // コンポーネントマネージャ
        $this->componentManager   = new ComponentManager($this);

        $this->init();
    }

    public function init ( ) {

        // 変数操作を組み込む
        $this->bind($this, array(
            'set' => '_set',
            'get' => '_get',
            'push' => '_push'
        ));

        // イベントを組み込む
        $this->bind('event',array(
            'on' => 'on',
            'off'=>'off',
            'trigger'=>'trigger'
        ));

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
    }


    public function _set ($name, $value) 
    {
        ArrayHelper::init($this->vars)->$name($value);
    }
    public function _get ($name, $default = null) 
    {
        return ArrayHelper::get($this->vars,$name,$default);
    }
    public function _push ($name, $value)
    {
        return ArrayHelper::push($this->vars,$name,$value);
    }


    public function call ($name, $params) {
        if ($this->helperManager->isCallable($name)) {
            return $this->helperManager->call($name, $params);
        }

        if ($this->componentManager->has($name)) {
            return Command::invoke(
                array($this->componentManager,'get'),
                $name, $params
            );
        }

        throw new \Exception("Invalid Call ".$name);
    }

    public function __call ($name, $params) {
        $return =  $this->call($name, $params);
        return $return;
    }

}
