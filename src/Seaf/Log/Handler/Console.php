<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Log\Handler;

use Seaf\Log;

/**
 * ロギングハンドラー（コンソール出力)
 */
class Console extends Log\Handler 
{

    public function _post ($context, $level = Log\Level::INFO) 
    {
        echo $this->makeMessage($context, $level)."\n";
    }

}
