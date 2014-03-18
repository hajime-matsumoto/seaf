<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Environment\Component;

use Seaf\Logger\Base;
use Seaf\Kernel\Kernel;

/**
 * Kernelに仕込むロガー
 */
class Logger extends Base
{
    public $name = "Environment";

    public function __construct ( )
    {
        parent::__construct();
    }

    /**
     * ヘルパ
     */
    public function helper ($name = null)
    {
        if (empty($name)) return $this;
        return $this->getHandler($name);
    }

    /**
     * _postをオーバライドする
     *
     * ログはカーネルを通して送出する
     * タグ名を変える
     *
     * @param int $level メッセージのレベル
     * @param array $params POSTに渡ってきた時の引数
     * @param array $opts メッセージのオプション
     * @param string $tag
     * @param array $trace
     */
    protected function _post ($level, $params, $opts = array(), $tag = null, $trace = array())
    {
        if ($tag == null) {
            $tag = $this->name;
        }
        Kernel::logger()->_post($level, $params, $opts, $tag, $trace);
    }
}
