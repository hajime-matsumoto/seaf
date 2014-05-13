<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\Mysql;

use Seaf\Database;
use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Base\Component;
use Seaf\Util\Util;

/**
 * Mysql操作ハンドラ
 */
class Handler extends Database\DatabaseHandler
{
    protected static $object_name = 'MySQL';
    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $parent, Database\DSN $dsn)
    {
        $this->setParentModule($parent);

        $p = $dsn->parse(true);

        $this->con = mysqli_connect(
            $p('host', 'localhost'),
            $p('user', 'root'),
            $p('passwd', ''),
            $p('db', ''),
            $p('port', '3306')
        );

        if ($this->con->connect_error) {
            throw new Exception\Exception([
                "DB接続エラー(%s):%s",
                $this->con->connect_errno,
                $this->con->connect_error
            ]);
        }

        $this->debug(['Start MySQL with %s', $dsn]);

    }

    /**
     * クエリを実行する
     */
    public function query ($query)
    {
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            $this->warn(["Query Error: [%s] %s", $query, $this->getLastError()]);
        }else{
            $this->debug("Query: $query");
        }

        if ($result === true || $result === false) {
            return new Result($result);
        } else {
            return new Cursor($result);
        }
    }

    /**
     * 最後のエラーを取得する
     */
    public function getLastError ( )
    {
        return mysqli_error($this->con);
    }

    /**
     * エスケープする
     */
    public function escapeVars ($datas)
    {
        if (!is_array($datas)) {
           return is_int($datas) ? intval($datas): mysqli_real_escape_string($this->con, $datas);
        }

        $safeVars = [];

        foreach ($datas as $k=>$v) {
            $safeVars[$k] = is_int($v) ? intval($v): mysqli_real_escape_string($this->con, $v);
        }
        return $safeVars;
    }

    /**
     * データを更新する
     */
    public function update ($table_name, $datas, $where, $limit = 1) 
    {
        $safeVars = $this->escapeVars($datas);

        $set = [];
        foreach ($safeVars as $k=>$v) {
            $set[] = "`$k`"." = ".(is_int($v) ? $v: "'$v'");
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE '.$this->buildWhere($where). ' Limit '. $limit,
            $table_name,
            implode(',', $set)
        );
        return $this->query($sql);
    }

    /**
     * 配列からテーブルを作成する
     *
     * @param array
     */
    public function find($table, $array)
    {
        $findQuery = Util::Dictionary($array);

        $sql = 'SELECT';

        $sql.= ' '.$this->buildFields($findQuery('fields'));
        $sql.= ' FROM `';
        $sql.= $this->escapeVars($table);
        $sql.= '`';

        $where = $this->buildWhere($findQuery('query'));

        if (!empty($where)) {
            $sql.= ' WHERE '.$where;
        }

        $sort = $this->buildSort($findQuery('sort'));
        if (!empty($sort)) {
            $sql.= ' ORDER BY ' . $sort;
        }

        $limit = $this->buildLimit($findQuery('limit'), $findQuery('offset'));

        if (!empty($limit)) {
            $sql.= ' LIMIT ' . $limit;
        }

        return $this->query($sql);
    }

    /**
     * 配列からテーブルを作成する
     *
     * @param array
     */
    public function createTable($table, $array)
    {
        $esc = function ($vars) {
            return $this->escapeVars($vars);
        };

        $cfg = Util::Dictionary($array);

        if ($cfg->dict('options')->get('useDrop',false)) {
            $sql = sprintf('DROP TABLE IF EXISTS `%s`;', $esc($table));
            $this->query($sql);
        }

        $sql = sprintf('CREATE TABLE IF NOT EXISTS `%s`', $esc($table));
        $sql.= " (\n";
        foreach ($cfg('fields', []) as $k=>$v)
        {
            $fields[] = sprintf('`%s` %s',
                $k, $this->buildCreateTableField($v)
            );
        }
        $sql.= implode(", \n", $fields);
        $sql.= ");";
        $this->query($sql);

        if ($cfg('primary_index',false)) {
            $sql = sprintf("ALTER TABLE `%s`", $esc($table));
            $sql.= sprintf(" ADD PRIMARY KEY (%s);", $cfg('primary_index'));
            $this->query($sql);

            if ($cfg->dict('options')->get('useAutoIncrement', false)) {
                $this->query(sprintf(
                    "ALTER TABLE `%s` MODIFY `%s` INT AUTO_INCREMENT",
                    $esc($table),
                    $esc($cfg('primary_index'))
                ));
            }
        }

        foreach ($cfg('indexes', []) as $index) {
            $sql = sprintf("ALTER TABLE `%s`", $table);
            $sql.= sprintf(" ADD INDEX (`%s`);", $esc($index['name']));
            $this->query($sql);
        }
    }

    protected function buildCreateTableField ($v)
    {
        $map = [
            'string' => 'VARCHAR',
            'int' => 'INT'
        ];

        if (is_string($v)) {
            $type = $v;
        }elseif (is_array($v)) {
            $v = Util::Dictionary($v);
            $type = $map[$v('type')];
            $length = $v('size');
        }
        return $type . (!empty($length) && $length>0 ? '('.$length.')': '');
    }

    /**
     * 配列からINSERTを実行する
     *
     * @param name
     * @param array
     */
    public function insert($table_name, $datas)
    {
        $safeVars = $this->escapeVars($datas);
        foreach ($safeVars as $k=>$v) {
            $fields[] = "`$k`";
            $values[] = is_int($v) ? $v: "'$v'";
        }
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s);',
            $table_name,
            implode(',', $fields),
            implode(',', $values)
        );
        return $this->query($sql);
    }

    public function lastInsertId($table_name)
    {
        $result = $this->query(
            sprintf(
                'SELECT LAST_INSERT_ID() FROM %s',
                $this->escapeVars($table_name)
            )
        );

        if ($result->isError()) {
            return false;
        }

        $result->setFetchMode('num');
        $arr = $result->fetch();
        $lastId = $arr[0];
        if (is_numeric($lastId)) {
            $lastId = intval($lastId);
        }
        return $lastId;
    }

    // ---------------------------------------------
    // Buildシリーズ
    // ---------------------------------------------
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
        $sql = '`'.key($value)."` ". $this->buildWhereValue(current($value));
        return $sql;
    }

    protected function buildWhereValue($value)
    {
        if (!is_array($value)) {
            $value = $this->escapeVars($value);
            return '= ' . (is_int($value) ? intval($value): "'$value'");
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
        return $con.' '.$this->escapeVars(current($value));
    }

    protected function buildSort ($sort)
    {
        if (empty($sort)) return '';

        if (!is_array($sort)) $sort = [$sort];

        $parts = [];
        foreach ($sort as $k=>$v) {
            if (is_numeric($k)) {
                $parts[] = sprintf(
                    '`%s`', 
                    $this->escapeVars($v)
                );
                continue;
            }

            $v = $v > 0? true: false;
            $parts[] = sprintf(
                '`%s` %s',
                $this->escapeVars($k),
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
