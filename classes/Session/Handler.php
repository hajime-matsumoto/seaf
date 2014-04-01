<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Session;

use Seaf;
use Seaf\Data\Container\ArrayContainer;

class Handler extends ArrayContainer
{
    static $COOKIE_KEY = 'SEAF_SESSION';

    protected $sessionid = null;
    private $kvs;

    /**
     * Sessionを作成する
     */
    public static function factory ($config)
    {
        $c = new ArrayContainer($config);
        $type = $c('type', 'file');

        // KVSを立ち上げる
        $kvs = Seaf::ReflectionMethod(
            'Seaf\\KVS\\Storage', 'factory'
        )->invoke(null, $config);

        $kvs->use_sha1_for_key = false;

        $handler = new self();
        $handler->kvs = $kvs;
        return $handler;
    }

    /**
     * 既に使われているセッションか
     */
    public function isSessionUsed($sessionid)
    {
        if ($this->kvs->has($sessionid)) {
            return true;
        }
        return false;
    }

    public function sessionStart()
    {
        $sessionid = $this->sessionid;

        if (!$this->kvs->has($sessionid)) {
            $this->kvs->put($sessionid, array());
        }else{
            $this->data = unserialize($this->kvs->get($sessionid));
        }
    }

    public function sessionStore() 
    {
        $sessionid = $this->sessionid;

        $this->kvs->put($sessionid, serialize($this->data));
    }

    public function sessionDestroy()
    {
        $sessionid = $this->sessionid;

        $this->kvs->del($sessionid);
    }

    /**
     * セッションをスタートさせる
     *
     * @param string 固定セッション
     * @return string セッションID
     */
    public function start ($sessionid = null)
    {
        if ($sessionid == null) {
            // セッションキー
            $sessionid = $this->getSessionID();
        }
        // スタート時の処理
        $this->sessionid = $sessionid;

        $this->sessionStart();

        // PHP終了時にSessionStoreを呼ぶ
        register_shutdown_function(array($this,'sessionStore'));

        return $sessionid;
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
        $this->sessionStart();

        return $sessionid;
    }

    /**
     * セッションIDを取得する
     */
    private function getSessionID ( )
    {
        if (Seaf::Cookie()->has(self::$COOKIE_KEY)) {
            return Seaf::Cookie()->get(self::$COOKIE_KEY);
        }

        $sessionid = $this->generateID();

        // セッションをクッキーに設定する
        Seaf::Cookie()->set(self::$COOKIE_KEY, $sessionid);

        return $sessionid;
    }

    private function generateID()
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
}
