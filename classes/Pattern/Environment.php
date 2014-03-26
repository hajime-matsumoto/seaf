<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Pattern;

use Seaf\DI;
use Seaf\Exception;
use Seaf\Data\Container;

/**
 * 環境パターン
 *
 * 動的なメソッド拡張と、イベント機能、DIパターンの機能を
 * 統合したトレイト
 */
trait Environment
{
    use DynamicMethod;
    use Event;

    /**
     * @var DI\Container
     */
    protected $di;

    /**
     * @var Container\ArrayContainer
     */
    protected $registry;

    /**
     * 初期化処理
     */
    public function initEnvironment( )
    {

        // DynamicMethodパターンを初期化
        // --------------------------------------
        $this->initDynamicMethod( );

        // Eventパターンを初期化
        // --------------------------------------
        $this->initEvent( );

        // インスタンスを作成
        // --------------------------------------
        $this->registry = new Container\ArrayContainer();
        $this->di       = new DI\Container($this);

        // 必要なメソッドを拡張する
        // --------------------------------------
        $this->bind($this, array(
            'di' => '_di'

        ))->bind($this->di, array(
            'register' => 'register'

        ))->bind($this->registry, array(
            'get' => 'get',
            'set' => 'set'
        ));
    }

    /**
     * DI
     *
     * @param string|null
     * @param array
     * @return mixed
     */
    public function _di($name = null, $params = array())
    {
        if ($name == null) return $this->di;
        return $this->di->call($name, $params);
    }

    /**
     * コール
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function call ($name, $params) 
    {
        return call_user_func_array(array($this,$name), $params);
    }

    /**
     * Calling Fall Back
     *
     * @param string
     * @param array
     * @return mixed
     */
    public function callFallBack ($name, $params)
    {
        if ($this->di->has($name)) {
            return $this->di->call($name, $params);
        }

        throw new Exception\InvalidCall($name, $this);
    }
}
