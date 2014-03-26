<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Session\Handler;

use Seaf;
use Seaf\Data\Container\ArrayContainer;

abstract class Handler extends ArrayContainer
{
    static $COOKIE_KEY = 'SEAF_SESSION';

    protected $sessionid = null;

    /**
     * セッションをスタートさせる
     */
    public function start ( )
    {
        // セッションキー
        $sessionid = $this->getSessionID();

        // スタート時の処理
        $this->sessionid = $sessionid;
        $this->sessionStart();

        // PHP終了時にSessionStoreを呼ぶ
        register_shutdown_function(array($this,'sessionStore'));
    }

    /**
     * セッションIDを新しく生成する
     *
     */
    public function regenerateID( )
    {
        $sessionid = $this->generateID();

        // セッションを破棄する
        $this->sessionDestroy( );

        // クッキーを上書きする
        Seaf::Cookie()->set(self::$COOKIE_KEY, $sessionid);

        // セッションIDを上書きする
        $this->sessionid = $sessionid;
    }

    /**
     * セッションIDを取得する
     */
    public function getSessionID ( )
    {
        $g = Seaf::Globals();

        if (Seaf::Cookie()->has(self::$COOKIE_KEY)) {
            return Seaf::Cookie()->get(self::$COOKIE_KEY);
        }

        $sessionid = $this->generateID();

        // セッションをクッキーに設定する
        Seaf::Cookie()->set(self::$COOKIE_KEY, $sessionid);

        return $sessionid;
    }

    public function generateID()
    {
        // ランダム文字列を発生させる
        do {
            $sessionid = Seaf::Secure()->randomString(32);
        } while ($this->isSessionUsed($sessionid));

        return $sessionid;
    }

    /**
     * 分割したセッションを取得する
     */
    public function helper($name = null)
    {
        if ($name == null) return $this;

        if (!isset($this->data[$name])) {
            $this->data[$name] = array();
        }
        $container = new ArrayContainer();
        $container->data =& $this->data[$name];
        return $container;
    }


    /**
     * 既に使われているセッションか
     */
    abstract public function isSessionUsed($sessionid);
    abstract public function sessionStart();
    abstract public function sessionStore();
    abstract public function sessionDestroy();
}
