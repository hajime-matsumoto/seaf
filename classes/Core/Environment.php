<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core;

use Seaf\Pattern;

class Environment
{
    use Pattern\Environment;

    public function __construct ( )
    {
        // ---------------------------------
        // 環境を初期化する
        // ---------------------------------
        $this->initEnvironment( );

        // ---------------------------------
        // オートロードを追加する
        // ---------------------------------
        $this->di->factory->configAutoLoad(
            __NAMESPACE__.'\\Component\\'
        );
    }
}
