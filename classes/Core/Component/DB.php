<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Pattern;
use Seaf\DB\TransactionHandler as Base;
use Seaf\DB\DB as DBConnector;

/**
 * データベース
 */
class DB extends Base
{
    /**
     * 作成するメソッド
     *
     * @param array
     */
    public static function componentFactory ( )
    {
        $trh = new self();
        $c = Seaf::Config('db.handlers', array());
        foreach($c as $k=>$v) {
            $trh->setDBHandler($k, DBConnector::connect($v['dsn']));
        }
        $trh->setCacheHandler(
            $trh->makeCacheHandler(
                Seaf::Config('db.cache')
            )
        );
        return $trh;
    }
}
