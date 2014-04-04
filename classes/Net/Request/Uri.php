<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Net\Request;

use Seaf\Data\Container;
/**
 * URIを管理するクラス
 */
class Uri extends Container\ArrayContainer
{
    /**
     * 初期値
     *
     * @var array
     */
    private $defaults = array(
        'scheme'   => 'http',
        'host'     => 'localhost',
        'user'     => '',
        'pass'     => '',
        'path'     => '/',
        'query'    => '',
        'fragment' => '',
        'method' => 'GET'
    );

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        parent::__construct($this->defaults);
    }

    /**
     * メソッドを取得する
     *
     * @return string
     */
    public function method ( )
    {
        return $this['method'];
    }

    /**
     * パスを取得する。
     * マスクがある場合はマスクをかける。
     *
     * @return string
     */
    public function path ( )
    {
        if ($this->has('mask')) {
            if (
                '/' !== $this->mask &&
                0 === strpos($this->path, $this->mask)
            ) {
                $path = substr($this->path, strlen($this->mask));
                return empty($path) ? '/': $path;
            }
        }
        return $this['path'];
    }

    /**
     * URIを取得
     *
     * @param string
     */
    public function abs ($uri)
    {
        return rtrim($this->get('mask',''),'/').'/'.ltrim($uri,'/');
    }

    /**
     * URIをセットする
     *
     * @param string
     */
    public function setUri ($uri)
    {
        $parts = array();

        if (false !== $p = strpos($uri, ' ')) {
            list($method,$uri) = explode(' ', $uri, 2);
            $this['method'] = $method;
        }

        $parts = parse_url($uri);
        foreach ($parts as $k=>$v) {
            $this[$k] = $v;
        }

        return $this;
    }
}
