<?php
/**
 * Seaf 汎用パターン
 */
namespace Seaf\Pattern;;

use Seaf\Kernel\Kernel;

/**
 * 動的メソッド機能を与える
 */
trait DynamicMethod {

    /**
     * メソッドを保存する
     * @var array
     */
    protected $maps = array();

    /**
     * メソッドをマップする
     *
     * @param string|array $name
     * @param mixed  $action = false
     * @return object $this
     */
    public function map ($name, $action = false)
    {
        if (is_array($name)) {
            foreach($name as $k=>$v) {
                $this->map($k, $v);
            }
            return $this;
        }

        if (is_string($action) && !is_callable($action)) {
            $action = array($this,$action);
        }

        $this->maps[$name] = $action;
        return $this;
    }

    /**
     * メソッドがマップされているか
     *
     * @return bool
     */
    public function isMaped ($name)
    {
        return isset($this->maps[$name]);
    }

    /**
     * 動的メソッドをコールする
     * マップされていないメソッドが呼ばれた場合
     * callFallBackに転送する
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call ($name, $params)
    {
        if ($this->isMaped($name)) {
            $action = $this->maps[$name];

            // ディスパッチする
            return Kernel::dispatcher($action, $params, $this)->dispatch();
        }
        return $this->callFallBack($name, $params);
    }

    /**
     * __call出来なかった時によばれるメソッド
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    abstract function callFallBack ($name, $params);

}
