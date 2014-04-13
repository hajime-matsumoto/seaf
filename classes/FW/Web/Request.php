<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;

use Seaf;

/**
 * アプリケーションルータ
 */
class Request extends \Seaf\FW\Request
{
    public function __construct ( )
    {
        parent::__construct( );

        $g = Seaf::Globals();

        $this->path($g('_SERVER.REQUEST_URI', '/'));
        $this->method($g('_SERVER.REQUEST_METHOD', 'GET'));

        $this->set($g('_REQUEST', array()));
        if ($this->getMethod() == 'PUT') {
            $content = file_get_contents('php://input');
            parse_str($content, $params);
            $this->set($params);
        }
    }
}
