<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\MongoDB;

use Seaf\Base;
use Seaf\Data;

class Table
{
    public $handler;
    public $table;

    public function __construct (MongoDBHandler $handler, $name)
    {
        $this->table = $handler->db->$name;
        $this->handler = $handler;
    }

    /**
     * データを挿入する
     */
    public function insert ($datas)
    {
        return $this->makeResult($this->table->insert($datas));
    }

    /**
     * テーブルを削除する
     */
    public function drop ( )
    {
        return $this->makeResult($this->table->drop());
    }

    /**
     * インデックスを作成する
     */
    public function ensureIndex($indexes)
    {
        return $this->makeResult($this->table->ensureIndex($indexes));
    }

    /**
     * 結果を取得作成する
     */
    protected function makeResult($result)
    {
        return new Result($result, $this->handler->getLastError());
    }

    /**
     * データを検索する
     */
    public function find ($query)
    {
        return new FindQuery($this, $query);
    }

    public function realFind($findQuery)
    {
        $cur = $this->table->find($findQuery->query);
        if (!empty($findQuery->sort)) {
            $cur->sort($findQuery->sort);
        }
        if (!empty($findQuery->limit)) {
            $cur->limit($findQuery->limit);
        }
        if (!empty($findQuery->offset)) {
            $cur->skip($findQuery->offset);
        }
        return $cur;
    }
}
