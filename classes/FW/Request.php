<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW;


/**
 * アプリケーションルータ
 */
class Request extends \Seaf\Request\Request
{
    use \Seaf\Core\Component\ComponentTrait;

    public function _componentHelper($uri, $params = [])
    {
        if (empty($uri)) return $this;

        if (false === $p = strpos($uri, ' ')) {
            $this->path($uri);
        }else{
            $this->path(substr($uri, $p+1));
            $this->method(substr($uri, 0, $p));
        }
        $this->param($params);
    }
}
