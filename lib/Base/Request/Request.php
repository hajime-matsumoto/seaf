<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Request;

/**
 * リクエスト管理クラス
 */
class Request
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * パラメタ
     *
     * @var array
     */
    public $params;

    /**
     * パス
     *
     * @var array
     */
    public $path;

    /**
     * メソッド
     *
     * @var array
     */
    public $method;

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        $this->clear();
    }

    /**
     * クリア
     */
    public function clear ( )
    {
        $this->params = [];
        $this->method = static::METHOD_GET;
        $this->path   = '/';
        return $this;
    }

    /**
     * リクエストパスを設定する
     *
     * @param string
     * @return Request
     */
    public function path ($path)
    {
        $this->path = $path;
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
     * メソッドをセットする
     *
     * @param string
     * @return Request
     */
    public function method ($method)
    {
        $this->method = $method;
        return $this;
    }
}
