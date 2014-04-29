<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Base;
use Seaf\Data;
use Seaf\Container;

class Table
{

    use Container\MethodContainerTrait;

    /**
     * @var DBHandler
     */
    public $handler;

    /**
     * @var string
     */
    public $table_name;

    /**
     * @param DBHandler
     * @param string
     */
    public function __construct (DBHandler $handler, $name)
    {
        $this->table_name = $name;
        $this->handler = $handler;
    }

    /**
     * データを挿入する
     */
    public function insert ($datas)
    {
        return $this->handler->execute(
            Request::factory('insert')->tableName($this->table_name)->param($datas)
        );
    }

    /**
     * データを更新する
     */
    public function update ($datas, $where)
    {
        return $this->handler->execute(
            Request::factory('update')
            ->tableName($this->table_name)
            ->param('set',$datas)
            ->param('query', $where)
        );
    }

    /**
     * テーブルを削除する
     */
    public function drop ( )
    {
        return $this->handler->execute(
            Request::factory('drop')->tableName($this->table_name)
        );
    }

    /**
     * テーブルを作成する
     */
    public function create ($schema)
    {
        return $this->handler->execute(
            Request::factory('create')
                ->tableName($this->table_name)
                ->param('schema', $schema)
        );
    }


    /**
     * データを検索する
     */
    public function find ($query)
    {
        return new FindQuery($this, $query, [$this, 'outputFilter']);
    }

    /**
     * １件だけデータを取得する
     */
    public function findOne ($query)
    {
        return $this->outputFilter($this->find($query)->limit(1)->execute()->getNext());
    }

    /**
     * OutPutFilter
     */
    public function outputFilter($array)
    {
        if ($this->hasMethod('outputFilter')) {
            return $this->callMethod('outputFilter', $array);
        }
        return $array;
    }


    /**
     * 実際に検索をする
     */
    public function realFind($findQuery)
    {
        $result = $this->handler->execute(
            Request::factory('find')->tableName($this->table_name)->param('findQuery', $findQuery)
        );
        return $result;
    }
}
