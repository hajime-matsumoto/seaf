<?php
namespace Seaf\Core\Component;

use Seaf\Pattern\DynamicMethod;
use Seaf\Exception;

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
            'header' => '_header'
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
     * For DynamicMethod
     */
    public function callFallBack($name, $params)
    {
        throw new Exception\InvalidCall($name, $this);
    }
}
