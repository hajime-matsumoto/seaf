<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger;

use Seaf\Logger\Base;

class Handler extends Base
{
    public $name;
    public $logger;

    /**
     * @param string
     * @param Base
     */
    public function __construct ($name, Base $logger)
    {
        $this->name = $name;
        $this->logger = $logger;

        parent::__construct();
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
        $tag = $this->name;
        $this->logger->_post($level, $params, $opts, $tag, $trace);
    }

    public function helper ($name = null)
    {
        if ($name == null) return $this;
        return $this->logger->getHandler($this->name.'>'.$name);
    }
}
