<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource\Handler;

use Seaf;
use Seaf\Base;
use Seaf\DataSource;
use Seaf\Container\ArrayContainer;


/**
 * MysqlDB用のハンドラ
 */
class MysqlHandler extends RDB
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
     * コンストラクタ
     *
     * @param string|array
     */
    public function __construct (DataSource\DSN $dsn, DataSource\DataSource $ds)
    {
        parent::__construct($dsn, $ds);

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
     * Fetch
     */
    public function fetch ($result)
    {
        return $result->fetch_array(MYSQLI_ASSOC);
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
            return $this->multiQuery($query);
        }

        return $this->createResult(
            $this->query($query),
            [
                'query'=>$query
            ]
        );
    }

    /*
     * インサートリクエストを処理する
     *
     * @param Request
     */
    public function insertRequest (DataSource\Request $request)
    {
        // テーブル名の取得
        $table = $request->getPath( );

        // パラメタの取得
        $raw_params = $request->getParams();

        // パラメタのエスケープ
        $escaped_params = $this->escapeParam($raw_params, null, $request->getSchema());

        // フィールド名と値に分ける
        array_walk($escaped_params, function($value, $field) use($request, &$fields, &$values) {
            $fields[] = '`'.$field.'`';
            $values[] = $this->quoteParam($value, $field, $request->getSchema());
        });

        // INSERT QUERYを生成
        $sql = $this->safeSprintf(
            'INSERT INTO `%s` (%s) VALUES (%s);',
            $table,
            implode(',', $fields),
            implode(',', $values)
        );

        // クエリの実行
        return $this->createResult(
            $this->query($sql),
            [
                'query' => $sql
            ]
        );
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
        $escaped_params = $this->escapeParam($raw_params, null, $request->getSchema());

        // フィールド名=値にする
        array_walk($escaped_params, function($value, $field) use($request, &$parts) {
            $parts[] = $this->safeSprintf(
                "`%s` = %s",
                $field,
                $this->quoteParam($value, $field, $request->getSchema())
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
    public function findRequest (DataSource\Request $request)
    {
        // テーブル名の取得
        $table = $request->getPath();

        // QUERYを生成
        $sql = $this->safeSprintf(
            'SELECT %s FROM %s',
            $this->buildFields($request->getFields()),
            $table
        );

        // WHERE句を生成
        $sql.= $this->buildWhere($request->getWhereParts());

        // ORDER句を生成
        $sql.= $this->buildOrder($request->getOrder());

        // LIMIT句を生成
        $sql.= $this->buildLimit($request->getLimit(), $request->getOffset());

        $sql.=';';

        // クエリの実行
        return $this->createResult(
            $this->query($sql),
            ['sql'=>$sql]
        );
    }





    private function createResult($res, $log = [], $isError = null, $errorMsg = null)
    {
        if ($isError === null) {
            $isError = $res === false;
            $errorMsg = $this->con->error;
        }

        return new DataSource\Result(
            $res,
            $this,
            $isError,
            $errorMsg,
            $log
        );
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
    protected function escapeParam ($raw_param, $data_type = null, DataSource\Schema $schema = null)
    {
        if (is_array($raw_param)) {
            $escaped_params = [];
            foreach ($raw_param as $key=>$value) {

                if ($schema != null) {
                    $data_type = $this->convertEscapeDataType($schema->fields[$key]['type']);
                } else {
                    $data_type = 'str';
                }
                $escaped_params[$key] = $this->escapeParam($value, $data_type);
            }
            return $escaped_params;
        }

        if ($data_type == null) {
            $data_type = 'str';
        } elseif ($data_type == 'int') {
            $raw_param = intval($raw_param);
        }


        return mysqli_real_escape_string($this->con, $raw_param);
    }

    /**
     * パラメタをクオートする
     *
     * @param array $raw_params
     */
    protected function quoteParam ($param, $type = null, $field = null, DB\Schema $schema = null)
    {
        if (is_array($param)) {
            $params = [];
            foreach ($param as $key=>$value) {
                $params[$key] = $this->quoteParam($value, $type, $key, $schema);
            }
            return $params;
        }

        if ($schema == null) {
            if ($type == null) {
                if (is_int($param)) {
                    $data_type = int;
                }
                $data_type = 'str';
            } else {
                $data_type = $type;
            }
        } else {
            $data_type = $this->convertEscapeDataType($schema->fields[$field]['type']);
        }

        if ($data_type == 'int') {
            return $param;
        }else{
            return '"'.$param.'"';
        }
    }

    /**
     * 安全なsprintf
     */
    protected function safeSprintf ($format)
    {
        return vsprintf($format, array_slice(func_get_args(),1));
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
    public function createTable(DataSource\Schema $schema, $doDropTable = false)
    {
        $table = $schema->table;

        // クリエイトテーブルをする前にドロップテーブルする
        if ($doDropTable) $this->dropTable($table);

        $sql = $this->safeSprintf("CREATE TABLE IF NOT EXISTS `%s` (\n", $table);

        // フィールドを生成
        foreach ($schema->fields as $field=>$attrs) {
            $a = new ArrayContainer($attrs);

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
            $a = new ArrayContainer($attrs);

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

        $log = [
            'sql'=>$sql,
            'doDropTable'=>$doDropTable
        ];

        $this->debug($log);

        return $this->createResult(
            $this->multiQuery(trim($sql)),
            [
                'sql'=>$sql,
                'doDropTable'=>$doDropTable
            ]
        );
    }

    protected function convertEscapeDataType($type)
    {
        switch ($type) {
        case 'int':
            return 'int';
            break;
        case 'varchar':
        default:
            return 'str';
            break;
        }
    }

    protected function convertType($type) 
    {
        $my_type = self::$dataTypeMap[strtolower($type)];

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
    protected function dropTable($table) {
        return $this->query(
            $this->safeSprintf(
                'DROP TABLE IF EXISTS `%s`;',
                $table
            )
        );
    }
}
