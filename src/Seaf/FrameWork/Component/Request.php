<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork\Component;

use Seaf\FrameWork\Application;
use Seaf\Helper\ArrayHelper;

/**
 * Request
 */
class Request
{
    protected $uri = '/';
    protected $method = 'GET';
    protected $app;
    protected $params;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->init();
    }

    public function init ($request = null)
    {
        if ($request !== null) {
            $parts = parse_url($request);
            $this->setUri($parts['path']);
        }
    }

    public function setUri($path)
    {
        $this->uri = $path;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setParams($params)
    {
        foreach ($params as $k => $v) {
            $this->setParam($k, $v);
        }
    }

    public function setParam($k,$v)
    {
        $this->params[$k] = $v;
    }

    public function getParam($k,$default=null)
    {
        return ArrayHelper::get($this->params, $k, $default);
    }

    public function __get($k) {
        return $this->getParam($k);
    }

    public function __set($k, $v) {
        return $this->setParam($k, $v);
    }
}
