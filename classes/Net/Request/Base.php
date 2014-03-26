<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Net\Request;

use Seaf;
use Seaf\Data\Container;

/**
 * アプリケーションリクエスト
 */
class Base extends Container\ArrayContainer
{
    /**
     * @var URI
     */
    private $uri = null;

    /**
     * Contruct
     */
    public function __construct ( )
    {
        $this->uri = new Uri();
    }

    /**
     * URIを取得する
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * クローンを取得する
     */
    public function getClone()
    {
        $clone = clone $this;
        return $clone;
    }

    /**
     * クローン時の処理
     */
    public function __clone()
    {
        $this->uri = clone $this->uri;
    }
}
