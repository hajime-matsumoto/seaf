<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\DB\Handler;

/**
 * データベース
 */
class DB extends Handler
{
    /**
     * 遅延束縛
     */
    protected static function who( ) {
        return __CLASS__;
    }

    /**
     * 作成するメソッド
     *
     * @param array
     */
    public static function componentFactory ( )
    {
        return static::Factory(Seaf::Config('database'));
    }

    public function helper ($name = null)
    {
        if ($name == null) return $this;
        return $this->open($name);
    }
}
