<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

/**
 * データベースモジュール
 */
class DB
{
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_STR = 'str';

    /**
     * 接続する
     *
     * @param string
     * @return Con
     */
    public static function connect ($dsn)
    {
        // DSNパーサを立ち上げる
        $dsn = new DSN($dsn);

        // コネクションクラスをビルドする
        $type = $dsn->getType();
        $class = __NAMESPACE__.'\\'.ucfirst($type).'\\Con';
        $con = new $class($dsn);

        return $con->handler();
    }
}
