<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Net\Request\Initializer;

use Seaf\Net\Request;
use Seaf\Kernel\Kernel;

/**
 * WEB用のイニシャライザ
 */
class Web extends Request\Initializer
{
    /**
     * Kernel::Globals()->getHelper()
     * @param Seaf\Data\Helper
     */
    private $g;

    /**
     * Kernel::Globals()->getHelper()->SERVER
     * @param Seaf\Data\Helper
     */
    private $s;

    /**
        'QUERY_STRING' => string '' (length=0)
        'SCRIPT_NAME' => string '/index.php' (length=10)
        'REQUEST_URI' => string '/contact' (length=8)
        'DOCUMENT_URI' => string '/index.php' (length=10)
        'DOCUMENT_ROOT' => string '/project/dev/mmizui/public' (length=26)
        'SERVER_PROTOCOL' => string 'HTTP/1.0' (length=8)
        'SERVER_ADDR' => string '127.0.0.1' (length=9)
        'SERVER_PORT' => string '20001' (length=5)
        'SERVER_NAME' => string 'mmizui.hazime.org' (length=17)
        'HTTPS' => string '' (length=0)
        'HTTP_HOST' => string 'localhost:20001' (length=15)
        'HTTP_USER_AGENT' => string 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36' (length=109)
        'HTTP_REFERER' => string 'http://192.168.11.200:9000/index' (length=32)
        **/

    public function init ()
    {
        $g = $this->g = Kernel::Globals()->getHelper();
        $s = $this->s = $this->g->SERVER;

        // URIを構築する
        $uri = $this->getURI();

        // METHOD
        $method = $this->getMethod();

        // BASE_URIを作成する
        $base_uri = dirname($s('SCRIPT_NAME'));

        if ($method == 'PUT') {
            parse_str(file_get_contents('php://input'), $params);
            $this->base->set($params);
        }

        $this->base->uri = Request\Uri::factory(array('uri' => $uri));
        $this->base->base_uri = $base_uri;
        $this->base->method = $method;
        $this->base->set($g('REQUEST'));
    }

    public function getMethod ()
    {
        $g = $this->g;

        $method      = $g->REQUEST('_method');
        if ($method->isEmpty()) {
            $method = $g->SERVER(
                array('HTTP_X_HTTP_METHOD_OVERRIDE', 'REQUEST_METHOD'),
                'GET'
            );
        }
        return $method;
    }

    public function getURI ()
    {
        $s = $this->s;
        
        $uri = sprintf(
            '%s://%s%s%s',
            $s->SERVER_PROTOCOL->regex('/HTTPS/','https','http'),
            $s('SERVER_NAME'),
            $s('SERVER_PORT')->not(80, ':%s', ''),
            $s('REQUEST_URI')
        );

        return $uri;
    }
}
