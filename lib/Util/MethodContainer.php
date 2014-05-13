<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Util;

use Seaf\Base\Event;

/**
 * メソッドコンテナ
 */
class MethodContainer implements Event\ObservableIF
{
    use Event\ObservableTrait;
    private $data;

    public function __construct($default = [])
    {
        $this->data = Util::Dictionary();

        foreach ($default as $k=>$v) {
            $this->set($k, $v);
        }
    }

    public function set($name, callable $action)
    {
        $this->data->prepend($name, $action);
    }

    public function restore($name)
    {
        $act = $this->data->shift($name);
        if ($this->data->isEmpty($name)) {
            $this->set($name, $act);
        }
    }

    public function has($name)
    {
        return $this->data->isEmpty($name) ? false: true;
    }

    public function callArray($name, $args)
    {
        return call_user_func_array(current($this->data->get($name)), $args);
    }

    public function __call($name, $args)
    {
        return $this->callArray($name, $args);
    }

}
