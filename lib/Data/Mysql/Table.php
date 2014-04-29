<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\Mysql;

use Seaf\Base;
use Seaf\Data;
use Seaf\Registry\Registry;

/**
 * データベーステーブル
 */
class Table extends Data\DB\ProductTable
{
    public $handler;
    public $table;

    public function __construct (MysqlHandler $handler, $name)
    {
        $this->table_name = $name;
        $this->handler = $handler;
    }

    public function getLastError ( )
    {
        return $this->handler->getLastError();
    }

    /**
     * データを挿入する
     */
    public function insert ($datas) 
    {
        $safeVars = $this->handler->escapeVars($datas);
        foreach ($safeVars as $k=>$v) {
            $fields[] = "`$k`";
            $values[] = is_int($v) ? $v: "'$v'";
        }
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s);',
            $this->table_name,
            implode(',', $fields),
            implode(',', $values)
        );
        $result = $this->handler->query($sql);
        return $this->makeResult($result, $this->handler->getLastError());
    }

    /**
     * テーブルを削除する
     */
    public function drop ( ) 
    {
        $sql = sprintf(
            'DROP TABLE IF EXISTS `%s`;',
            $this->table_name
        );
        $result = $this->handler->query($sql);
        return $this->makeResult($result, $this->handler->getLastError());
    }

    /**
     * テーブルを作成する
     */
    public function create ($schema)
    {
        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS `%s` ',
            $this->handler->escapeVars($this->table_name)
        );

        $sql.= '(';
        foreach ($schema['fields'] as $k=>$v)
        {
            $k = $this->handler->escapeVars($k);
            $v = $this->handler->escapeVars($v);

            $fields[] = sprintf('`%s` %s',
                $k, $this->buildCreateTableField($v)
            );
        }
        $sql.= implode(',', $fields);
        $sql.= ')';

        $result = $this->handler->query($sql);
        return $this->makeResult($result, $this->handler->getLastError());
    }

    protected function buildCreateTableField ($v)
    {
        if (is_string($v)) {
            $type = $v;
        }elseif (is_array($v)) {
            $v = seaf_container($v);
            $type = $v['type'];
            $length = $v['length'];
        }
        return $type . (!empty($length) ? '('.$length.')': '');
    }

    /**
     * 結果を取得作成する
     */
    protected function makeResult($result)
    {
        new Result ($result);
    }

    /**
     * FindQueryでデータを検索する
     */
    public function realFind($findQuery)
    {
        $sql = 'SELECT';

        $sql.= ' '.$this->buildFields($findQuery->fields);
        $sql.= ' FROM `';
        $sql.= $this->handler->escapeVars($this->table_name);
        $sql.= '`';

        $where = $this->buildWhere($findQuery->query);

        if (!empty($where)) {
            $sql.= ' WHERE '.$where;
        }

        $sort = $this->buildSort($findQuery->sort);
        if (!empty($sort)) {
            $sql.= ' ORDER BY ' . $sort;
        }

        $limit = $this->buildLimit($findQuery->limit, $findQuery->offset);
        if (!empty($limit)) {
            $sql.= ' LIMIT ' . $limit;
        }

        return $this->cur = new Cursor($this->handler, $sql);
    }

    protected function buildWhere ($array)
    {
        if (empty($array)) return '';

        $sql = '';
        foreach ($array as $k=>$v) {
            if ($k == '$or') {
                $sql.= $this->buildWhereOrCouse($v);
            }else{
                $sql.= $this->buildWherePear([$k=>$v]);
            }
        }
        return $sql;
    }

    protected function buildWhereOrCouse ($array)
    {
        $parts = [];
        foreach ($array as $pear) {
            $parts[] = $this->buildWhere($pear);
        }
        return implode(' OR ', $parts);
    }
    protected function buildWherePear($value)
    {
        $sql = key($value)." ". $this->buildWhereValue(current($value));
        return $sql;
    }

    protected function buildWhereValue($value)
    {
        if (!is_array($value)) {
            return $this->handler->escapeVars($value);
        }
        switch (key($value)) {
        case '$gt':
            $con = '>';
            break;
        case '$lt':
            $con = '<';
            break;
        default:
            $con = '=';
            break;
        }
        return $con.' '.$this->buildWhereValue(current($value));
    }

    protected function buildSort ($sort)
    {
        if (empty($sort)) return '';

        $parts = [];
        foreach ($sort as $k=>$v) {
            $parts[] = sprintf(
                '`%s` %s',
                $this->handler->escapeVars($k),
                ((bool)$v ? 'ASC': 'DESC')
            );
        }
        return implode(',', $parts);
    }

    protected function buildLimit ($limit, $offset)
    {
        if (empty($limit) && empty($offset)) return '';

        $limit = intval($limit);
        $offset = intval($offset);

        if (!empty($limit) && !empty($offset)) {
            return sprintf("%s,%s", $offset, $limit);
        }
        if (!empty($limit) && empty($offset)) {
            return sprintf("%s", $limit);
        }
        if (empty($limit) && !empty($offset)) {
            return sprintf("%s", $offset);
        }
    }

    protected function buildFields ($fields)
    {
        if (empty($fields)) return '*';
    }
}
