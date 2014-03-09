<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Log\Handler;

use Seaf\Log;

require_once  __DIR__.'/../lib/php-console-master/src/PhpConsole/__autoload.php';

/**
 * ロギングハンドラー（コールバック)
 */
class PhpConsole extends Log\Handler {

    private $connector;

    public function __construct ($config) {
        parent::__construct($config);

        $this->connector = \PhpConsole\Connector::getInstance( );
    }

    public function _post ($context, $level = Log\Level::INFO) {
        \PhpConsole\Handler::getInstance()->debug($context, Log\Level::$map[$level]);
    }
}
