<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web\Component;

use Seaf;
use Seaf\FW;
use Seaf\Data\Container;

/**
 * アプリケーションリクエスト
 */
class Request extends FW\Component\Request
{
    public function __construct ( )
    {
        parent::__construct();
        $g = Seaf::Globals();

        $this->uri()->setURI($g('_SERVER.REQUEST_URI', '/'));
        $this->uri()->set('method', $g('_SERVER.REQUEST_METHOD', 'GET'));

        $this->set($g('_REQUEST', array()));
        if ($this->uri()->method() == 'PUT') {
            $content = file_get_contents('php://input');
            parse_str($content, $params);
            $this->set($params);
        }
    }
}
