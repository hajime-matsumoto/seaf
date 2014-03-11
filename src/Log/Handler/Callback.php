<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Log\Handler;

use Seaf\Log;

/**
 * ロギングハンドラー（コールバック)
 */
class Callback extends Log\Handler {

    private $cb;

    public function __construct ($config) {
        parent::__construct($config);

        $this->cb = $config['callback'];
    }

    public function _post ($context, $level = Log\Level::INFO) {
        call_user_func($this->cb, $this->makeMessage($context, $level), $context);
    }

}
