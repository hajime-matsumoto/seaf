<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Logger\Writer;

use Seaf\Exception;
use Seaf\Helper;

class EchoWriter extends Base
{
    protected $buf = array();

    /**
     * コンストラクタ
     *
     * @param array $config
     * @return Base
     */
    protected function __construct ($config)
    {
    }

    public function _post($message)
    {
        $this->buf[]= $message;
    }

    public function __destruct()
    {
        echo implode("\n", $this->buf);
    }

}
