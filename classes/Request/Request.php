<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Request;

use Seaf\Container\ArrayContainer;
use Seaf\Base;

/**
 * リクエスト管理クラス
 */
class Request extends ArrayContainer
{
    use Base\RecurseCallTrait;

    private $path     = '/';
    private $pathMask = false;
    private $method   = 'GET';

    public function __construct ( )
    {

    }

    // セッター
    // --------------------------------

    /**
     * リクエストパス
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
     * リクエストマスク
     *
     * @param string
     * @return Request
     */
    public function pathMask ($mask) 
    {
        $this->pathMask = $mask;
        return $this;
    }


    /**
     * メソッド
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
     * パラメタ
     *
     * @param string
     * @param mixed
     * @return Request
     */
    public function param($name, $value = null)
    {
        if ($this->recurseCallIfArray($name, __FUNCTION__)) return $this;

        $this->set($name, $value);
        return $this;
    }

    // ゲッター
    // --------------------------------

    /**
     * リクエストパスを取得
     *
     * @param bool
     * @param string
     * @return string
     */
    public function getPath($addPath = '', $useMask = true)
    {
        if (!empty($addPath)) {
            $path =  rtrim($this->pathMask,'/').'/'.ltrim($addPath,'/');
        }elseif (
            $useMask && 
            $this->pathMask && $this->pathMask != '/' &&
            0 === strpos($this->path, $this->pathMask)) {
            $path = substr($this->path, strlen($this->pathMask));
        } else {
            $path = $this->path;
        }
        return empty($path) ? '/': $path;
    }


    /**
     * リクエストメソッドを取得
     *
     * @return string
     */
    public function getMethod( )
    {
        return $this->method;
    }

    public function getParams( )
    {
        return $this->data;
    }

    public static function who ( )
    {
        return __CLASS__;
    }

    public static function factory ( )
    {
        $class = static::who();
        $request = new $class();
        return $request;
    }

}
