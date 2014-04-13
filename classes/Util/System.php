<?php
namespace Seaf\Core\Component;

use Seaf\Pattern\DynamicMethod;
use Seaf\Exception;
use Seaf;

/**
 * システムユーティリティ
 * ----------------------
 */
class System
{
    use DynamicMethod;

    /**
     * コンストラクタ
     */
    public function __construct ()
    {
        $this->bind($this, array(
            'halt' => '_halt',
            'header' => '_header',
            'setcookie' => '_setcookie'
        ));
    }

    /**
     * シャットダウン
     * 
     * @param string
     * @return void
     */
    public function _halt ($data = '')
    {
        exit($data);
    }

    /**
     * ヘッダーを送信する
     *
     * @param string $string
     * @param bool $replace = null
     * @param int $code = null
     */
    public function _header($string, $replace = null, $code = null)
    {
        if ($replace === null && $code === null) {
            header($string);
        }elseif($code === null){
            header($string, $replace);
        }else{
            header($string, $replace, $code);
        }
    }

    /**
     * クッキーを設定する
     *
     * @param string
     * @param string
     * @param int
     */
    public function _setcookie($name, $value, $expire = 0)
    {
        setcookie($name, $value, $expire, '/');
    }

    /**
     * For DynamicMethod
     */
    public function callFallBack($name, $params)
    {
        throw new Exception\InvalidCall($name, $this);
    }

    /**
     *
     */
    public function printf ($format)
    {
        echo call_user_func_array([$this,'sprintf'], func_get_args());
    }

    /**
     *
     */
    public function printfn ($format)
    {
        echo call_user_func_array([$this,'sprintfn'], func_get_args());
    }

    public function sprintfn ($format) {
        $ENDLINE = Seaf::Globals('argc') > 0 ? "\n": "<br />";
        return call_user_func_array([$this,'sprintf'], func_get_args()).$ENDLINE;
    }

    public function sprintf ($format) {
        if (func_num_args() == 1) return $format;

        return vsprintf($format, array_slice(func_get_args(),1));
    }

    /**
     * 指定メソッドのクロージャを取得する
     *
     * @param string
     */
    public function getClosure ($method)
    {
        return Seaf::ReflectionMethod($this, $method)->getClosure($this);
    }

}
