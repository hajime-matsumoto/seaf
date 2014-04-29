<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\MongoDB;

use Seaf\Base;
use Seaf\Data;

class Table extends Data\DB\ProductTable
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
     * テーブルを作成する
     */
    public function create ($array)
    {
        $indexes=[];
        foreach($array['indexes'] as $idx) {
            $c = seaf_container($idx);
            $indexes[$c('name')] = $c('desc', false) ? -1: 1;
        }
        return $this->ensureIndex($indexes);
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
