<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Com\Result;

class Result
{
    /**
     * パラメタ
     *
     * @var array
     */
    public $params = [];

    /**
     * @var string
     */
    protected $status = StatusCode::OK;

    /**
     * @var string
     */
    protected $body;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->clear();
    }

    /**
     * 結果をクリアする
     *
     * @return Result
     */
    public function clear ( )
    {
        $this->body   = '';
        $this->params = [];
        return $this;
    }

    /**
     * ステータスコードをセットする
     *
     * @param string
     * @return Result
     */
    public function status($code)
    {
        $this->status = $code;
        return $this;
    }
    /**
     * 結果本文を書き込む
     *
     * @param string
     * @return Result
     */
    public function write($body)
    {
        $this->body .= $body;
        return $this;
    }

    /**
     * パラメタをセットする
     *
     * @param string
     * @param mixed
     * @return Request
     */
    public function param ($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k=>$v) $this->param($k, $v);
            return $this;
        }

        $this->params[$name] = $value;
        return $this;
    }

    //---------------------------------------
    // 取得系
    //---------------------------------------

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

    /**
     * レスポンスコードを取得する
     *
     * @return int
     */
    public function getStatus( )
    {
        return $this->status;
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
            'params'  => $this->getParams(),
            'body'    => $this->body
        );
        return $array;
    }

    /**
     * Jsonにする
     */
    public function toJson( )
    {
        $array = $this->toArray();
        return json_encode($array);
    }
}
