<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Pattern;

/**
 * イベント機能用のトレイト
 *
 * <code>
 * $this->on('event1', Closure{});
 * $this->trigger('event1');
 * $this->triggerArgs('event1', array());
 * </code>
 */
trait Event
{
    /**
     * イベント保持
     *
     * @var array
     */
    protected $events = array();

    /**
     * 初期化処理
     */
    public function initEvent ( )
    {
        $this->events = array();
    }

    /**
     * イベントトリガ (引数を配列で渡せる)
     *
     * @param string $name
     * @param array $args
     */
    public function triggerArgs ($name, $args)
    {
        if (isset($this->events[$name]) && is_array($this->events[$name]))
        foreach ($this->events[$name] as $handler) {
            $continue = call_user_func_array($handler, $args);

            if ($continue === false) break;
        }
    }

    /**
     * イベントトリガ
     *
     * @param string $name
     * @param mixed $arg,...
     */
    public function trigger ($name)
    {
        $args = array_slice(func_get_args(),1);
        return $this->triggerArgs($name, $args);
    }

    /**
     * イベントを設定する
     *
     * @param string $name
     * @param mixed $handler コールバック
     * @param bool $append Appendするならtrue
     */
    public function on ($name, $handler, $append = true)
    {
        if (is_string($handler) && !is_callable($handler)) {
            $handler = array($this, $handler);
        }

        if ($append == true) {
            $this->events[$name][] = $handler;
        }else{
            if (!isset($this->events[$name])) $this->events[$name] = array();
            array_unshift($this->events[$name], $handler);
        }

        return $this;
    }

    /**
     * イベントを解除する
     *
     * @param string $name
     * @param mixed $handler コールバック
     */
    public function off ($name, $handler)
    {
        foreach($this->events[$name] as $k=>$v) {
            if ($handler == $v) {
                unset($this->events[$name][$k]);
            }
        }
    }

    /**
     * イベントを全て解除する
     *
     * @param string $name
     */
    public function clearEvents($name)
    {
        $this->events[$name] = array();
    }
}
