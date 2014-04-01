<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger\Writer;

use Seaf\Exception;
use Seaf\Helper;

class EchoWriter extends Base
{
    protected $buf = array();
    private $useHtml = false;

    public function _post($message)
    {
        $this->buf[]= $message;
    }

    public function shutdown()
    {
        if (!$this->useHtml) {
            echo "\n";
            echo implode("\n", $this->buf);
        } else {
            if (empty($this->buf)) return;
            echo "<pre>";
            echo implode("\n", $this->buf);
            echo "</pre>";
        }

    }

    public function configUseHtml($useHtml)
    {
        if ($useHtml == 'false') $useHtml = false;
        $this->useHtml = $useHtml;
    }
}
