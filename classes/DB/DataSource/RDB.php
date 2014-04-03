<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\DataSource;

use Seaf;
use Seaf\DB;
use Seaf\Exception;

/**
 * Relational Data Base用の抽象クラス
 */
abstract class RDB extends DB\DataSource
{
    public function multiQuery ($query)
    {
        if (false !== strpos($query, ';')) {
            $sql = strtok($query,';');
            do {
                $result = $this->query(trim($sql).';');
                if ($this->isError($result)) {
                    $error = $this->getError($result);
                    throw new Exception\Exception([
                        'QueryError: %s', $error
                    ]);
                }
            } while($sql = strtok(';'));
            return $result = true;
        }

        return $this->query($query);
    }
    /**
     * クエリを処理する
     *
     * @param Request
     */
    public function query ($query)
    {
        $result = $this->realQuery($query);

        if ($this->isError($result)) {
            throw new Exception\Exception([
                'QueryError: %s %s',
                $query,
                $this->getError($result)
            ]);
        }

        // クエリのログを書く
        Seaf::Logger('DB')->info('Query:'.$query);
        return $result;
    }

    /**
     * クエリを処理する
     *
     * @param Request
     */
    abstract protected function realQuery ($query);

    /**
     * Where句をビルドする
     *
     * @param array
     */
    protected function buildWhere ($where)
    {
        if (empty($where)) return '';

        $sql = ' WHERE';
        foreach ($where as $field => $value)
        {
            $parts[] = $this->safeSprintf(
                '%s %s %s',
                $field,
                '=',
                $this->quoteParam(
                    $this->escapeParam($value),
                    $field
                )
            );
        }
        $sql.= " ".implode(" ", $parts);
        return $sql;
    }

    /**
     * Limit句をビルドする
     *
     * @param array
     */
    protected function buildLimit ($limit, $offset)
    {
        if ($limit > 0 && $offset >0) {
            $sql= sprintf(' LIMIT (%s, %s)',
                intval($limit),
                intval($offset)
            );
        } elseif ($limit > 0 && $offset < 0) {
            $sql= sprintf(' LIMIT %s',
                intval($limit)
            );
        } elseif ($limit < 0 && $offset > 0) {
            $sql= sprintf(' OFFSET %s',
                intval($limit)
            );
        } else {
            $sql = '';
        }

        return $sql;
    }

    /**
     * ORDER BY句をビルドする
     *
     * @param array
     */
    protected function buildOrder ($order)
    {
        if (empty($order)) return '';

        $parts = [];
        foreach ($order as $field=>$option) {
            $parts[] = $this->safeSprintf('`%s` %s',$field,$option);
        }
        return ' ORDER BY '.implode(', ', $parts);
    }

    /**
     * Fields句をビルドする
     *
     * @param array
     */
    protected function buildFields ($fields)
    {
        if (empty($fields)) return '*';
        return implode(', ',$fields);
    }
}
