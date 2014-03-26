<?php
/**
 * Seaf 汎用パターン
 */
namespace Seaf\Pattern;

/**
 * メソッドを動的に設定するパターン
 *
 * <code>
 * $some->map('test', function ( ){ });
 * $some->test();
 * </code>
 */
trait DynamicMethod {

    /**
     * メソッドを保存する
     * @var array
     */
    protected $maps = array();

    public function initDynamicMethod ( )
    {
        $this->maps = array();
    }

    /**
     * メソッドを取得する
     */
    public function getMethods ( )
    {
        $methods = get_class_methods($this);
        return $methods + array_keys($this->maps);
    }

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
     * メソッドをバインドする
     *
     * @param object
     * @param array $list
     * @return object $this
     */
    public function bind ($object, $list)
    {
        foreach ($list as $k=>$v) {
            $this->map($k, array($object, $v));
        }
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
            return call_user_func_array($action, $params);
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
    abstract protected function callFallBack ($name, $params);
}
