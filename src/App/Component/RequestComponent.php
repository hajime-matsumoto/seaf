<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\App\Component;

use Seaf\Core\Environment;

/**
 * アプリケーションリクエスト
 * ===========================
 *
 * 要件
 * --------------------------
 * * getURI
 * * getMethod
 */
class RequestComponent
{
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';

    const DEFAULT_URI    = '/';
    const DEFAULT_METHOD = self::METHOD_GET;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    public $method;

    /**
     * @var Environment
     */
    private $env;

    public function initComponent (Environment $env) 
    {
        $this->env = $env;
    }

    /**
     * init
     *
     * @param $config = falss
     * @return void
     */
    public function init ($config = false)
    {
        if (!is_array($config)) {
            $this->initDefault();
        }

        if (isset($config['uri'])) {
            $this->uri($config['uri']);
        }

        if (isset($config['method'])) {
            $this->method($config['method']);
        }
    }

    /**
     * method
     *
     * @param $method = null
     * @return void
     */
    public function initDefault()
    {
        $this->uri(self::DEFAULT_URI);
        $this->method(self::DEFAULT_METHOD);
    }

    /**
     * uri
     *
     * @param $uri = null
     * @return void
     */
    public function uri ($uri = null)
    {
        if ($uri == null) return $this->uri;
        $this->uri = $uri;
        return $this;
    }

    /**
     * method
     *
     * @param $method = null
     * @return void
     */
    public function method ($method = null)
    {
        if ($method == null) return $this->method;
        $this->method = $method;
        return $this;
    }
}
