<?php
namespace Seaf\Kernel\Module;

use Seaf\Exception\Exception;
use Seaf\Kernel\Kernel;
use Seaf\Pattern\DynamicMethod;

/**
 * System関連の操作
 */
class System extends Module
{
    /**
     * DynamicMethodパターンを導入
     */
    use DynamicMethod;

    /**
     * DynamicMethod が呼び出せなかった場合の処理
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function callFallBack ($name, $params)
    {
        new Exception(array('%sは%sに登録されていない呼び出しです', $name, __CLASS__));
    }

    /**
     * モジュールの初期化
     */
    public function initModule (Kernel $kernel)
    {
        $this->map(array(
            'halt' =>  '_halt',
            'header' =>  '_header'
        ));
    }

    /**
     * スクリプトをシャットダウンする
     *
     * @param $body
     */
    public function _halt ($body = 0)
    {
        exit($body);
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
}
