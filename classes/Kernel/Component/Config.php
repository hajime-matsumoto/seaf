<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Kernel\Component;

use Seaf\Config\Base;
use Seaf\Kernel\Kernel;

/**
 * コンフィグ
 */
class Config extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
        parent::__construct( );
    }

    /**
     * 環境名を取得する
     */
    protected function envname ( )
    {
        return Kernel::$envname;
    }
}
