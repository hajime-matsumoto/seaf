<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Event;

use Seaf\Kernel\Kernel;

class Base
{
    /**
     * イベント保持
     *
     * @var array
     */
    protected $events;

    /**
     * イベントトリガ
     *
     * @param string $name
     * @param mixed $arg,...
     */
    public function trigger ($name)
    {
        if (isset($this->events[$name]) && is_array($this->events[$name]))
        foreach ($this->events[$name] as $handler) {
            $continue = Kernel::dispatcher(
                $handler, 
                array_slice(func_get_args(),1),
                $this
            )->dispatch();

            if ($continue === false) break;
        }
    }

    /**
     * ハンドラを加工する
     *
     * @param mixed $handler コールバック
     * @return mixed
     */
    protected function fixHandler ($handler)
    {
        return $handler;
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
        $handler = $this->fixHandler($handler);

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
    public function clear($name)
    {
        $this->events[$name] = array();
    }
}
