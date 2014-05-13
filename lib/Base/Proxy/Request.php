<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * プロキシコマンド
 */
namespace Seaf\Base\Proxy;

use Seaf\Util\Util;

/**
 * リクエスト
 */
class Request implements ProxyRequestIF
{
    private $handler;
    protected $params;
    private $prev;

    public function copyRequest(ProxyRequestIF $req)
    {
        $this->setHandler($req->handler());
        foreach($req->params() as $k  => $v) {
            $this->setParam($k, $v);
        }
    }


    /**
     * ハンドラに設定をしてもらう
     */
    public function __get($name)
    {
        return $this->handler( )->__proxyRequestGet($this, $name);
    }

    /**
     * ハンドラに設定をしてもらう
     */
    public function __call($name, $params)
    {
        // ハンドラのコールを呼び出しリザルトを得る
        $result = $this->executeProxyRequestCall($name, $params);

        if ($result->isError())
        {
            throw new \Exception($result->getError());
        }

        // リザルトセットから戻値を取得する
        return $result->retrive();
    }
    public function executeProxyRequestCall($name, $params)
    {
        $result = $this->handler( )->__proxyRequestCall($this, $name, $params);

        if (!($result instanceof ProxyResultIF)) {
            throw new \Exception(sprintf(
                "%s は ProxyResultIFではありません。",
                getType($result)
            ));
        }


        return $result;
    }

    /**
     * クローン時の処理
     */
    public function __clone( )
    {
        // 以前の情報を保存
        $this->prev = clone $this->params();

        // データ配列
        $this->params = clone $this->params();
    }

    /**
     * 以前の状態を復元
     */
    public function restore( )
    {
        $this->params = $this->prev;
    }

    /**
     * ハンドラを設定する
     */
    public function setHandler(ProxyHandlerIF $handler)
    {
        $this->handler = $handler;
    }

    /**
     * パラメタをセットする
     */
    public function setParam($name, $value)
    {
        $this->params( )->set($name, $value);
    }

    /**
     * パラメタを取得する
     */
    public function hasParam($name)
    {
        return $this->params( )->has($name);
    }

    /**
     * パラメタを取得する
     */
    public function isEmptyParam($name)
    {
        return $this->params( )->isEmpty($name);
    }

    /**
     * パラメタを取得する
     */
    public function getParam($name, $default = null)
    {
        return $this->params( )->get($name, $default);
    }

    /**
     * パラメタを追記する
     */
    public function addParam($name, $value, $prepend = false)
    {
        if ($prepend) {
            return $this->params( )->prepend($name, $value);
        }
        return $this->params( )->append($name, $value);
    }

    /**
     * パラメタを取得する
     */
    public function popParam($name)
    {
        return $this->params( )->pop($name);
    }

    /**
     * パラメタを削除する
     */
    public function clearParam($name)
    {
        return $this->params( )->clear($name);
    }



    /**
     * ハンドラを取得する
     */
    protected function handler( )
    {
        if (!$this->handler) {
            throw new \Exception("Handlerが登録されていません");
        }
        return $this->handler;
    }

    /**
     * パラメータコンテナを取得
     */
    public function params( )
    {
        if (!$this->params) {
            $this->params = Util::Dictionary();
        }
        return $this->params;
    }
}
