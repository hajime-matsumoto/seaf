<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\View;

use Seaf\Container;

/**
 * ViewModel
 */
class ViewModel extends Container\ArrayContainer
{
    use Container\MethodContainerTrait;

    /**
     * コンストラクタ
     */
    public function __construct ($data = [])
    {
        parent::__construct($data);
    }

    /**
     * CALLをMethodContainerにつなぐ
     */
    public function __call ($name, $params)
    {
        if ($this->hasMethod($name)) {
            return $this->callMethodArray($name, $params);
        }
        throw new \Seaf\Exception\InvalidCall($name, $params, $this);
    }

    /**
     * エクストラト用の配列を取得
     */
    public function getExtractVars ( )
    {
        // データの中身はエクストラクタブル
        $base = $this->data;
        $base['vm'] = $this; // ビューモデルもエクストラクトする
        foreach ($this->getMethodsArray() as $k=>$v)
        {
            $base[$k] = seaf_closure($v);
        }
        return $base;
    }
}
