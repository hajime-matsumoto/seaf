<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Application\Web\Component;

use Seaf\Application\Component\Request as RequestBase;
use Seaf\Kernel\Kernel;

/**
 * アプリケーションリクエスト
 * ===========================
 *
 * --------------------------
 */
class Request extends RequestBase
{
    private $opts;

    public $uri;
    public $method;
    public $base_uri;

    public function initRequest ( ) 
    {
        $this->opts = array();

        $this->SERVER  = Kernel::Globals()->get('SERVER');
        $this->REQUEST = Kernel::Globals()->get('REQUEST');

        $uri = $this->serv('REQUEST_URI', '/');

        $method = $this->req('_method');

        if (empty($method)) {
            $method = $this->serv(
                array('HTTP_X_HTTP_METHOD_OVERRIDE', 'REQUEST_METHOD'),
                'GET'
            );
        }

        $base_uri = $this->serv('SCRIPT_NAME');

        if (false !== $path_info = $this->serv('PATH_INFO',false)) {
            $uri = $path_info;
        } else {
            $base_uri = dirname($base_uri);
            if (!empty($base_uri) && $base_uri != '/' && strpos($uri, $base_uri) === 0) {
                $uri = substr($uri, strlen($base_uri));
            }
        }

        $this->uri = $uri;
        $this->base_uri = $base_uri;
        $this->method = $method;
    }

    private function req ($name, $default = null)
    {
        if (isset($this->REQUEST[$name])) {
            return $this->REQUEST[$name];
        }
        return $default;
    }

    private function serv ($name, $default = null)
    {
        if (is_array($name)) {
            foreach ($name as $v) {
                $data = $this->serv($v);
                if (!empty($data)) return $data;
            }
            return $default;
        }
        if (isset($this->SERVER[$name])) {
            return $this->SERVER[$name];
        }
        return $default;
    }

}
