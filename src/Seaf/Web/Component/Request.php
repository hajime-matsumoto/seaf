<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web\Component;
use Seaf;
use Seaf\FrameWork;
use Seaf\Helper\ArrayHelper;

/**
 * Request
 */
class Request extends FrameWork\Component\Request
{
    public function init ($request = null)
    {
        $server = ArrayHelper::init($_SERVER);
        $request = ArrayHelper::init($_REQUEST);

        if (php_sapi_name() == 'cli-server') {
            $uri = $server->get('REQUEST_URI');
        }else{
            $uri = $server->get('REQUEST_URI');
            $base = $this->app->get('base.uri');
            if (0 === strpos($uri, $base)) {
                if (strlen($uri) == strlen($base)) $uri = '/';
                else $uri = substr($uri, strlen($base));
            }
        }


        // クエリの解決
        $parts = parse_url($uri);
        $uri = $parts['path'];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $params);
            $this->setParams($params);
        }

        $method = $server->get(
            'HTTP_X_HTTP_METHOD_OVERRIDE',
            $request->get(
                '_method',
                $server->get(
                    'REQUEST_METHOD',
                    'GET'
                )
            )
        );

        $this->setUri($uri);
        $this->setMethod($method);

        if ($method == 'PUT') {
            $data = file_get_contents('php://input');
            parse_str($data, $params);
            $this->setParams($params);
        }
        $this->setParams($_REQUEST + $_GET +$_POST);

    }
}
