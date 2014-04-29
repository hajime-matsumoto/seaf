<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Session;

use Seaf\Base;
use Seaf\Data\KeyValueStore;
use Seaf\Secure;
use Seaf\Cookie;
use Seaf\Registry;

/**
 * Session
 */
class Session
{
    use Base\SingletonTrait;
    use KeyValueStore\KVSUserTrait;

    /**
     * クッキー用のセッションID名
     */
    private $cookieSessionIdName = '2fxDfpWzco';

    /**
     * セッション
     */
    private $sessionId = '2fxDfpWzco';

    private $backendStorage;

    private $data;

    public static function who ( )
    {
        return __CLASS__;
    }

    public function __construct ( )
    {
        $this->data = seaf_container([]);
    }

    public function setup($cfg)
    {
        $cfg = seaf_container($cfg);

        // バックアップストレージをセットアップする
        $this->setupBackendStorage($cfg('table', 'session'), $cfg('type', null));
    }

    /**
     * @param string
     * @param string
     */
    protected function setupBackendStorage($table, $type = null) 
    {
        $this->backendStorage = $this->getKVSHandler()->table(
            $table,
            $type
        );
    }

    /**
     * セッションを開始する
     */
    public function sessionOpen ($sid = null)
    {
        Registry\Registry::registerShutdownFunction([$this, 'sessionClose']);

        if ($sid != null) {
            $this->sessionId = $sid;
        }else{
            // クッキーからセッションIDの取得を試みる
            $cookie = Cookie\Cookie::getSingleton( );
            $sid = $cookie->getParam($this->cookieSessionIdName);

            if (empty($sid)) {
                $sid = $this->generateSessionId( );
            }
            $this->sessionId = $sid;
        }

        $this->sessionDataRestore();
    }

    /**
     * セッションIDを生成する
     */
    public function generateSessionId ( )
    {
        return Secure\Util::randomSha1();
    }

    /**
     * セッションIDを再生成する
     */
    public function regenerateSessionId ( )
    {
        $this->sessionDataDestroy($this->sessionId);
        $this->sessionId = $this->generateSessionId();
    }

    /**
     * セッションをリストアする
     */
    public function sessionDataRestore( )
    {
        if ($this->backendStorage->has($this->sessionId)) {
            $this->data = $this->backendStorage->get($this->sessionId);
        }
    }

    /**
     * セッションを保存する
     */
    public function sessionDataSave( )
    {
        // セッションデータを保存する
        $this->backendStorage->set(
            $this->sessionId,
            $this->data
        );
    }

    /**
     * セッションを終了する
     */
    public function sessionClose ( )
    {
        // セッションデータを保存する
        $this->sessionDataSave();
    }

    /**
     * セッションを破壊する
     */
    public function sessionDataDestroy ( )
    {
        $this->backendStorage->del(
            $this->sessionId
        );
    }

    /**
     * データセクションを取得
     */
    public function section ($name)
    {
        return $this->data->section($name);
    }
}
