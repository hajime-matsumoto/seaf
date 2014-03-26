<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Component;

use Seaf\Net\Request\Base;
use Seaf\Data\Container;

/**
 * アプリケーションリクエスト
 */
class Request extends Base
{
    /**
     * ヘルパを追加する
     */
    public function helper ($uri = null)
    {
        if ($uri !== null) {
            $this->uri()->setUri($uri);
        }
        return $this;
    }
}
