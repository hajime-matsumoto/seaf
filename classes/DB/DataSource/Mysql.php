<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\DataSource;

use Seaf\DB;
use Seaf\Exception;
use Seaf\Util\ArrayHelper;

class Mysql extends RDB
{
    /**
     * データタイプマップ
     * @var array
     */
    private static $dataTypeMap = [
        'int' => 'INT',
        'str' => 'VARCHAR',
        'varchar' => 'VARCHAR',
        'timestamp' => 'TIMESTAMP',
        'enum' => 'ENUM'
    ];

    /**
     * @var Mysqli
     */
    private $con;

    /**
     * データソースを初期化する
     */
    public function initDataSource (DB\DSN $dsn)
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
     * クエリリクエストを処理する
     *
     * @param Request
     */
    public function queryRequest (DB\Request $request)
    {
        // クエリの取得
        $query = $request->getBody();

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

        return $result = $this->query($sql);

    }

    /**
     * インサートリクエストを処理する
     *
     * @param Request
     */
    public function insertRequest (DB\Request $request)
    {
        // テーブル名の取得
        $table = $this->requestTable($request);

        // パラメタの取得
        $raw_params = $request->getParams();

        // パラメタのエスケープ
        $escaped_params = $this->escapeParam($raw_params);

        // フィールド名と値に分ける
        array_walk($escaped_params, function($value, $field) use(&$fields, &$values) {
            $fields[] = '`'.$field.'`';
            $values[] = $this->quoteParam($value, $field);
        });

        // INSERT QUERYを生成
        $sql = $this->safeSprintf(
            'INSERT INTO `%s` (%s) VALUES (%s);',
            $table,
            implode(',', $fields),
            implode(',', $values)
        );

        // クエリの実行
        return $this->query($sql);
    }

    /**
     * 更新リクエストを処理する
     *
     * @param Request
     */
    public function updateRequest (DB\Request $request)
    {
        // テーブル名の取得
        $table = $this->requestTable($request);

        // パラメタの取得
        $raw_params = $request->getParams();

        // パラメタのエスケープ
        $escaped_params = $this->escapeParam($raw_params);

        // フィールド名=値にする
        array_walk($escaped_params, function($value, $field) use(&$parts) {
            $parts[] = $this->safeSprintf(
                "`%s` = %s",
                $field,
                $this->quoteParam($value, $field)
            );
        });

        // INSERT QUERYを生成
        $sql = $this->safeSprintf(
            'UPDATE %s SET %s',
            $table,
            implode(', ', $parts)
        );

        // WHERE句を生成
        $sql.= $this->buildWhere($request->getWhere());

        // ORDER句を生成
        $sql.= $this->buildOrder($request->getOrder());

        // LIMIT句を生成
        $sql.= $this->buildLimit($request->getLimit(), $request->getOffset());

        $sql.=';';

        // クエリの実行
        return $this->query($sql);
    }

    /**
     * 検索リクエストを処理する
     *
     * @param Request
     */
    public function findRequest (DB\Request $request)
    {
        // テーブル名の取得
        $table = $this->requestTable($request);

        // QUERYを生成
        $sql = $this->safeSprintf(
            'SELECT %s FROM %s',
            $this->buildFields($request->getFields()),
            $table
        );

        // WHERE句を生成
        $sql.= $this->buildWhere($request->getWhere());

        // ORDER句を生成
        $sql.= $this->buildOrder($request->getOrder());

        // LIMIT句を生成
        $sql.= $this->buildLimit($request->getLimit(), $request->getOffset());

        $sql.=';';

        // クエリの実行
        return $this->query($sql);
    }

    /**
     * 削除リクエストを処理する
     *
     * @param Request
     */
    public function deleteRequest (DB\Request $request)
    {
        // テーブル名の取得
        $table = $this->requestTable($request);

        // QUERYを生成
        $sql = $this->safeSprintf(
            'DELETE FROM %s',
            $table
        );

        // WHERE句を生成
        $sql.= $this->buildWhere($request->getWhere());

        // ORDER句を生成
        $sql.= $this->buildOrder($request->getOrder());

        // LIMIT句を生成
        $sql.= $this->buildLimit($request->getLimit(), $request->getOffset());

        // クエリの実行
        return $this->query($sql);
    }


    // ---------------------------------
    // utility
    // ---------------------------------
    
    /**
     * クエリを処理する
     *
     * @param Request
     */
    protected function realQuery ($query)
    {
        return mysqli_query($this->con, $query);
    }

    /**
     * パラメタをエスケープする
     *
     * @param array $raw_params
     */
    protected function escapeParam ($raw_param)
    {
        if (is_array($raw_param)) {
            $escaped_params = [];
            foreach ($raw_param as $key=>$value) {
                $escaped_params[$key] = $this->escapeParam($value);
            }
            return $escaped_params;
        }

        return mysqli_real_escape_string($this->con, $raw_param);
    }

    /**
     * パラメタをクオートする
     *
     * @param array $raw_params
     */
    protected function quoteParam ($param, $field = null)
    {
        if (is_array($param)) {
            $params = [];
            foreach ($param as $key=>$value) {
                $params[$key] = $this->escapeParam($value, $key);
            }
            return $params;
        }

        if (is_int($param)) {
            return $param;
        }
        return '"'.$param.'"';
    }

    /**
     * 安全なsprintf
     */
    protected function safeSprintf ($format)
    {
        return vsprintf($format, array_slice(func_get_args(),1));
    }

    // ---------------------------------
    // 結果処理関連
    // ---------------------------------

    /**
     * 結果がエラー判定
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

    // ---------------------------------
    // スキーマ関連
    // ---------------------------------

    /**
     * クリエイトテーブルをする
     *
     * @param Schema
     * @param bool $doDropTable
     */
    public function createTable(DB\Schema $schema, $doDropTable = false)
    {
        $table = $schema->table;

        // クリエイトテーブルをする前にドロップテーブルする
        if ($doDropTable) $this->dropTable($table);

        $sql = $this->safeSprintf("CREATE TABLE IF NOT EXISTS `%s` (\n", $table);

        // フィールドを生成
        foreach ($schema->fields as $field=>$attrs) {
            $a = ArrayHelper::container($attrs);

            $type = $this->convertType($a('type','str'));

            // enumの時の補正
            if ($type == 'ENUM') {

                foreach($a('options',[]) as $v){
                    $opts[] = "'".$v."'";
                };

                $a->set('size', implode(',',$opts));
            }

            $parts[] = $this->safeSprintf('`%s` %s%s %s %s',
                $field,
                $type,
                ($a('size') != false ? "(".$a('size').")": ''),
                $this->convertDefault($a('default')),
                ($a('nullable') == true ? '': 'NOT NULL')
            );
        }
        $sql.= implode(",\n", $parts)."\n";

        // プライマリーキーを設定する
        if ($schema->primary) {
            $sql.= $this->safeSprintf(",PRIMARY KEY(%s)\n",
                $schema->primary
            );
        }
        $sql.= ");\n"; // Create Table終了

        // インデックスを生成
        foreach ($schema->indexes as $field=>$attrs) 
        {
            $a = ArrayHelper::container($attrs);

            $sql.= $this->safeSprintf("CREATE %s INDEX `%s` ON `%s` (%s);\n",
                ($a('uniq') === true ? 'UNIQUE': ''),
                $field,
                $table,
                $a('field')
            );
        }

        // オートインクリメント処理
        if ($schema->autoIncrement) {
            $sql.= $this->safeSprintf(
                "ALTER TABLE %s MODIFY %s INT AUTO_INCREMENT;",
                $table,
                $schema->primary
            );
        }


        return $this->multiQuery(trim($sql));
    }

    protected function convertType($type) 
    {
        $my_type = ArrayHelper::get(self::$dataTypeMap,strtolower($type),false);
        if ($my_type === false) {
            throw new Exception\Exception([
                "タイプ%sが変換できません。",
                $type
            ]);
        }
        return $my_type;
    }

    protected function convertDefault($default) 
    {
        $ret_default = '';
        // if ($default === null) $ret_default = 'NULL';
        if ($default === false) $ret_default = 'FALSE';
        if ($default === true) $ret_default = 'TRUE';
        if ($default === "") $ret_default = '""';
        if ($ret_default) {
            return 'DEFAULT '.$ret_default;
        } else {
            return '';
        }
    }

    /**
     * ドロップテーブル
     *
     * @param string
     */
    public function dropTable($table) {
        $this->query(
            $this->safeSprintf(
                'DROP TABLE IF EXISTS `%s`;',
                $table
            )
        );
    }
}
