<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger\Writer;

use Seaf\Exception;
use Seaf\Helper;

class EchoWriter extends Base
{
    protected $buf = array();

    public function _post($message)
    {
        $this->buf[]= $message;
    }

    public function shutdown()
    {
        echo "\n";
        echo implode("\n", $this->buf);
    }
}
