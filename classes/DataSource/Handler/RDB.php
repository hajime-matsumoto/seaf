<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource\Handler;

use Seaf;
use Seaf\DataSource;
use Seaf\Exception;

/**
 * Relational Data Base用の抽象クラス
 */
abstract class RDB extends DataSource\DataSourceHandler
{
    public function multiQuery ($query)
    {
        if (false !== strpos($query, ';')) {
            $sql = strtok($query,';');
            do {
                $result = $this->query(trim($sql).';');
                if (!$result) {
                    $error = $this->getLastError( );
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
        /*

        if ($this->isError($result)) {
            throw new Exception\Exception([
                'QueryError: %s %s',
                $query,
                $this->getError($result)
            ]);
        }

        // クエリのログを書く
        Seaf::Logger('DB')->debug('Query:'.$query);
         */
        return $result;
    }

    /**
     * クエリを処理する
     *
     * @param Request
     */
    abstract protected function realQuery ($query);

    /**
     * WherePart句をビルドする
     *
     * @param array
     * @param string 'AND|OR'
     */
    protected function _buildWherePart ( $part, $con = ' ' )
    {
        $map = [
            '$and'=>' AND ',
            '$or'=>' OR '
        ];
        $sql_parts = [];
        foreach ($part as $k=>$v) {
            if (array_key_exists($k, $map)) {
                $sql_parts[] = $this->_buildWherePart($v, $map[$k]);
            } else {
                $sql_parts[] = $this->safeSprintf(
                    '`%s`%s', $k, $this->_buildWhereValue($v)
                );
            }
        }
        return " ".implode($con, $sql_parts);
    }

    /**
     * Where句用のValueを作る
     *
     * @param string
     * @return string
     */
    protected function _buildWhereValue($value)
    {
        $map = [
            '$eq' => ' = ',
            '$gt' => ' > ',
            '$lt' => ' < ',
            '$in' => ' in '
        ];

        if (is_int($value)) return " = ".intval($value);

        if (is_string($value)) return " = ".$this->quoteEscapeParam($value, 'str');

        if (is_array($value)) {
            $key = key($value);

            if (array_key_exists($key, $map)) {
                list($value, $type) = current($value);
                return $map[$key].$this->quoteEscapeParam($value, $type);
            }

            list($value, $type) = $value;
            return " = ".$this->quoteEscapeParam($value, $type);
        }
        throw new Exception\Exception([
            "%sは処理できませんでした", $value
        ]);
    }

    /**
     * Where句をビルドする
     *
     * @param array
     * @return string $sql
     */
    protected function buildWhere ($wheres)
    {
        if (empty($wheres)) return '';

        $sql = ' WHERE';
        foreach ($wheres as $where)
        {
            $sql .= $this->_buildWherePart($where);
        }
        return $sql;
    }

    protected function quoteEscapeParam ($value, $type)
    {
        return $this->quoteParam(
            $this->escapeParam($value, $type),
            $type
        );
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
