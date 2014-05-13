<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

use Seaf\Base\Container;
use Seaf\Base\Event;

/**
 * コマンドリクエスト
 */
class Request implements RequestIF,Event\ObservableIF
{
    use Event\ObservableTrait;

    private $result;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->data = new Container\ArrayContainer();
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * ターゲットを追加する
     */
    public function __get ($target)
    {
        $this->data->add('target', $target);
        return $this;
    }
    public function add($name, $value)
    {
        return $this->data->add($name, $value);
    }
    public function set($name, $value)
    {
        return $this->data->set($name, $value);
    }

    public function dict($name)
    {
        return $this->data->dict($name);
    }

    public function dump( )
    {
        return $this->data->dump();
    }


    public function __invoke ($name, $default = null)
    {
        return $this->data->get($name, $default);
    }


    public function result ( ) 
    {
        if (!$this->result) {
            $this->result = new Result();
            $this->result->addObserver($this);
        }
        return $this->result;
    }

    public function _execute($name, $args = array())
    {
        $this->data->set('method', $name);
        $this->data->set('args', $args);

        // 実行処理
        $this->fireEvent('execute', [
            'request' => $this
        ]);

        return $this->result();
    }

    public function __call($name, $args)
    {
        // 実行前にエラーフラグを消す
        $this->result()->resetError();

        $result = $this->_execute($name, $args);
        if ($result->isError()) return $result;
        return $result->pop('returnValue');
    }
}
