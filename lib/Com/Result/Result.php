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
    private $body;

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

    /**
     * 本文を取得する
     *
     * @return string
     */
    public function getBody ( )
    {
        return $this->body;
    }

    /**
     * パラメタを取得する
     *
     * @return array
     */
    public function getParams ( )
    {
        return $this->params;
    }
}
