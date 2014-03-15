<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Application\Component;

use Seaf\Exception\Exception;

/**
 * アプリケーションリクエストヘルパ
 */
class RequestHelper
{
    public function __construct (Request $req )
    {
        $this->req = $req;
    }

    public function __get($name)
    {
        if (isset($this->req[$name]))
        {
            return new RequestParam($this->req[$name]);
        }
        throw new Exception (array(
            '%sはリクエストに入っていません',
            $name
        ));
    }
}

class RequestParam 
{
    public function __construct ($data)
    {
        $this->data = $data;
    }

    public function eq ($data)
    {
        return $this->data == $data;
    }
}
