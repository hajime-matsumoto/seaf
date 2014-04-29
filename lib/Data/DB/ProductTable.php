<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Base;
use Seaf\Data;
use Seaf\Registry\Registry;

/**
 * データベーステーブル
 */
abstract class ProductTable
{
    /**
     * データを挿入する
     */
    abstract public function insert ($datas);

    /**
     * テーブルを削除する
     */
    abstract public function drop ( );

    /**
     * 結果を取得作成する
     */
    abstract protected function makeResult($result);

    /**
     * FindQueryでデータを検索する
     */
    abstract public function realFind($findQuery);

    /**
     * データを検索する
     */
    public function find ($query)
    {
        return new FindQuery($this, $query);
    }
}
