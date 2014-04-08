<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Net\Response;

use Seaf;

/**
 * Response クラス
 */
class Base
{
    /**
     * ステータスコード
     *
     * @param int
     */
    public $status  = 200;

    /**
     * レスポンスパラメタ
     *
     * @param array
     */
    public $params  = array();

    /**
     * レスポンスボディ
     *
     * @param array
     */
    public $body    = '';

    /**
     * レスポンスヘッダー
     *
     * @param array
     */
    public $headers = array();

    public $isSent = false;

    /**
     * レスポンスを初期化する
     */
    public function clear()
    {
        $this->status = 200;
        $this->body = '';
        $this->headers = array();
        $this->params = array();
        $this->isSent = false;
        return $this;
    }

    /**
     * レスポンスステータスを設定する
     *
     * @param int $code
     * @return $this
     */
    public function status ($code)
    {
        $this->status = $code;
        return $this;
    }

    /**
     * レスポンスボディに追記する
     *
     * @param string $body
     * @return $this
     */
    public function write ($body) 
    {
        $this->body.=$body;
        return $this;
    }

    /**
     * レスポンスパラメタを追加する
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function param ($name, $value = false)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->param($k, $v);
            return $this;
        }
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * レスポンスヘッダーを追加する
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function header ($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->header($k, $v);
            return $this;
        }
        $this->headers[$name] = $value;
        return $this;
    }

    //---------------------------------------
    // 取得系
    //---------------------------------------

    /**
     * レスポンスパラメタを取得する
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        return $this->params[$name];
    }

    /**
     * レスポンスパラメタを取得する
     *
     * @return array()
     */
    public function getParams( )
    {
        return $this->params;
    }


    /**
     * レスポンスbodyを取得する
     *
     * @return string
     */
    public function getBody( )
    {
        return $this->body;
    }

    //---------------------------------------
    // 変換系
    //---------------------------------------

    /**
     * 文字列にする
     */
    public function toString( )
    {
        $array = $this->toArray();
        return json_encode($array);
    }

    /**
     * 配列にする
     */
    public function toArray( )
    {
        $array = array(
            'status'  => $this->status,
            'params'  => $this->params,
            'body'    => $this->body
        );
        return $array;
    }

    //---------------------------------------
    // 送信系
    //---------------------------------------

    /**
     * 送信する
     */
    public function send ( ) {
        $this->isSent = true;
        $this->sendHeaders();
        Seaf::system()->halt($this->body);
    }

    /**
     * ヘッダを送信する
     */
    public function sendHeaders () {
        if (headers_sent()) {
            return false;
        }

        // Send status code header
        Seaf::system()->header(
            sprintf(
                '%s %d %s',
                Seaf::Globals('SERVER.SERVER_PROTOCOL', 'HTTP/1.1'),
                $this->status,
                StatusCode::$codes[$this->status]),
            true,
            $this->status
        );


        // Send other headers
        foreach ($this->headers as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    Seaf::system()->header($field.': '.$v, false);
                }
            } else {
                Seaf::system()->header($field.': '.$value);
            }
        }

        return $this;
    }

    /**
     * Jsonを送信する
     */
    public function sendJson( )
    {
        $json = json_encode($this->toArray( ));
        $this->clear();
        $this->header('Content-Type', 'application/json')->write($json)->send();
    }
}
