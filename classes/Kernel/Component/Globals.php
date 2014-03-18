<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Kernel\Component;

use Seaf\Helper\ArrayHelper;

/**
 * Global変数を取得する
 *
 * このクラスをかませる事で単体テストを可能にする
 */
class Globals extends ArrayHelper
{
    public function __construct ( )
    {
        parent::__construct($GLOBALS);
    }


    /**
     * ヘルパメソッド
     *
     * @param string $name = null
     * @return mixed
     */
    public function helper ($name = null, $default = null)
    {
        if ($name == null) return $this;

        return $this->get($name, $default);
    }

}
