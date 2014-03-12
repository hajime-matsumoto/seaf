<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Application\Component;

use Seaf;
use ArrayAccess;
use Seaf\Environment\Environment;

/**
 * アプリケーションリクエスト
 * ===========================
 *
 * 要件
 * --------------------------
 * * getURI
 * * getMethod
 */
class RequestComponent implements ArrayAccess
{
    public $uri;
    public $method;
    public $params;

    public function __invoke ($uri)
    {
        $this->parseUri($uri);
        return $this;
    }

    public function parseUri($uri)
    {
        if(false !== $p = strpos($uri, ' ')) {
            $method = trim(substr($uri,0,$p));
            $uri = trim(substr($uri,$p+1));
        }
        if (false !== strpos($uri, '?')) {
            $parts = parse_url($uri);
            $uri = $parts['path'];
            $params = array();
            parse_str($parts['query'], $params);
            $this->setParams($params);
        }

        $this->setUri($uri);
        $this->setMethod($method);
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParams($params)
    {
        foreach ($params as $k=>$v) {
            $this->setParam($k, $v);
        }
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function __toString( )
    {
        $array = array(
            'method' => $this->method,
            'uri'    => $this->uri,
            'params' => $this->params
        );
        return json_encode($array);
    }

    /** For ArrayAccess **/
    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }
    public function offsetGet($offset)
    {
        return $this->params[$offset];
    }
}
