<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Com\Request;

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
     * @var string
     */
    public $path;

    /**
     * パスマスク
     *
     * @var string
     */
    public $mask;

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
     * イニシャライズ
     */
    public function init ($request, $params = [])
    {
        $method = self::METHOD_GET;
        if(false !== strpos($request, ' ')) {
            list($method, $path) = explode(' ', $request, 2);
        }else{
            $path = $request;
        }
        $this
            ->clear()
            ->param($params)
            ->method($method)
            ->path($path);
    }

    /**
     * クリア
     */
    public function clear ( )
    {
        $this->params = seaf_container([]);
        $this->method = static::METHOD_GET;
        $this->path   = '/';
        $this->mask   = '';
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
     * Query文字列でパラメタを設定する
     *
     * @param string
     * @param mixed
     * @return Request
     */
    public function importQueryString ($query)
    {
        parse_str($query, $params);
        $this->param($params);
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

    /**
     * パスマスクをセットする
     *
     * @param string
     * @return Request
     */
    public function mask ($mask)
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * メソッドを取得する
     *
     * @return string
     */
    public function getMethod ( )
    {
        return $this->method;
    }

    /**
     * パスを取得する
     *
     * @return string
     */
    public function getPath ( )
    {
        if (
            !empty($this->mask) &&
            $this->mask !== '/' &&
            strpos($this->path, $this->mask) === 0
        ) {
            return '/'.trim(substr($this->path, strlen($this->mask)), '/');
        }
        return $this->path;
    }

    /**
     * パスを取得する
     *
     * @return string
     */
    public function getPathWithoutMask ( )
    {
        return $this->path;
    }

    /**
     * パスマスクを取得する
     *
     * @return string
     */
    public function getMask ( )
    {
        return $this->mask;
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

    /**
     * パラメタを取得する
     *
     * @param string
     */
    public function getParam ($name, $default = null)
    {
        return $this->params->getVar($name, $default);
    }
}
