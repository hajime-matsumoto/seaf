<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\Mysql;

use Seaf\DB;
use Seaf\Exception;

/**
 * Mysqlコネクション
 */
class Con extends DB\Con
{
    /**
     * コネクション
     */
    private $con;

    /**
     * 初期化
     *
     * @param DB\DSN
     */
    public function initCon (DB\DSN $dsn)
    {
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
    }

    /**
     * クエリを実行
     *
     * @param string
     * @return mixed result
     */
    public function query ($sql)
    {
        return mysqli_query($this->con, $sql);
    }

    /**
     * エラー判定
     *
     * @param mixed
     * @return bool
     */
    public function isError ($result)
    {
        if ($result === false) return true;
        return false;
    }

    /**
     * エラー取得
     *
     * @return string
     */
    public function getError ($result)
    {
        return $this->con->error;
    }

    /**
     * 連想配列でレコードを取得
     *
     * @param mixed
     * @return array
     */
    public function fetchAssoc ($result)
    {
        if (!is_object($result)) throw new Exception\Exception(
            '結果セットは存在しません'
        );
        return $result->fetch_array(MYSQLI_ASSOC);
    }

    // -------------------------------------
    // エスケープ
    // -------------------------------------

    /**
     * 文字列をエスケープする
     *
     * @param string
     * @param string
     * @return string
     */
    public function escapeValue ($value, $type)
    {
        $value = mysqli_real_escape_string($this->con, $value);

        switch ($type) {
        case DB\DB::DATA_TYPE_INT:
            $value = intval($value);
            break;
        case DB\DB::DATA_TYPE_STR:
            $value = sprintf('"%s"',$value);
            break;
        }

        return $value;
    }

    // -------------------------------------
    // モデル関係
    // -------------------------------------

    protected function createTableColSql($col) 
    {
        $sql = '';
        switch ($col['type']) {
        case 'varchar':
            $type = 'VARCHAR';
            break;
        case 'int':
            $type = 'INT';
            break;
        case 'timestamp':
            $type = 'TIMESTAMP';
            break;
        case 'enum':
            $type = 'ENUM';
            $type.= '(';
            $opts = array();
            foreach ($col['options'] as $opt) {
                $opts[] = '\''.$opt.'\'';
            }
            $type.= implode(",", $opts).')';
            break;
        default:
            throw new Exception\Exception([
                '%sを変換できません', $col['type']
            ]);
        }

        $sql = "$type";
        if (isset($col['size'])) {
            $sql .= '('.$col['size'].')';
        }

        if (isset($col['primary']) && $col['primary'] === true) {
            $sql .= ' PRIMARY KEY';
            if (isset($col['auto_increment']) && $col['auto_increment'] === true) {
                $sql .= ' AUTO_INCREMENT';
            }
        }

        return $sql;
    }

}
