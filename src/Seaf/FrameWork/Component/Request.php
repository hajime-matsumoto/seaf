<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FrameWork\Component;

use Seaf\FrameWork\Application;

/**
 * Request
 */
class Request
{
    private $uri = '/';
    private $method = 'GET';

    public function __construct( $request = null )
    {
        $this->init($request);
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
}
