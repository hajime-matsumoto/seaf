<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * Sqlビルドツール
 */
class SqlBuilder implements HaveHandlerIF
{
    use HaveHandler;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $model_class = false;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $fields;

    /**
     * @var array
     */
    private $wheres = [];

    /**
     * @var array
     */
    private $insert_values = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * colums
     */
    private $declear_columns = [];

    /**
     * result
     * @var Result
     */
    private $result;

    /**
     * コンストラクタ
     */
    public function __construct ($sql = null)
    {
        if ($sql != null) {
            $this->parse($sql);
        }
    }

    /**
     * クリア
     */
    public function clear ( )
    {
        // タイプとテーブルはクリアしない
        $this->fields = [];
        $this->wheres = [];
        $this->insert_values = [];
        $this->values = [];
        return $this;
    }

    /**
     * SQLを解析する
     *
     * @param string
     */
    public function parse ($sql)
    {
        if (preg_match('/
            (?<type>select)\s
            (?<fields>.+)\s
            FROM\s
            (?<table>.+)
            (
                WHERE\s
                (?<wheres>.+)
            ){0,1}
            /ix', $sql, $m)) {
            $this
                ->type($m['type'])
                ->fields($m['fields'])
                ->table($m['table']);
            if (isset($m['wheres'])) {
                $this->wheres[] = $m['wheres'];
            }
            return $this;
        }

        if (preg_match('/
            (?<type>insert)\s*INTO
            \s*(?<table>[^\s]+)\s*
            \(\s*(?<fields>[^\)]+)\s*\)\s*
            VALUES\s*
            \(\s*(?<values>[^\)]+)\s*\)\s*
            /ix', $sql, $m)) {
            $this
                ->type($m['type'])
                ->fields($m['fields'])
                ->table($m['table']);
            $this->insert_values[] = $m['values'];
            return $this;
        }


        throw new Exception\Exception([
            '%sはパースできません',
            $sql
        ]);
    }

    /**
     * カラム宣言を取り込む
     */
    public function declearColumns ($declears)
    {
        foreach ($declears as $k=>$v) {
            $this->declearColumn($k, $v);
        }
        return $this;
    }

    /**
     * カラム宣言
     */
    public function declearColumn ($name, $declear)
    {
        if ($name{0} !== ':') $name = ":$name";
        $this->declear_columns[$name] = $declear;
        return $this;
    }

    /**
     * 処理タイプ
     */
    public function type ($type)
    {
        $this->type = strtoupper($type);
        return $this;
    }

    /**
     * フィールド
     */
    public function fields ($fields)
    {
        $this->fields[] = $fields;
        return $this;
    }

    /**
     * values
     */
    public function values ($values)
    {
        $this->insert_values[] = $values;
        return $this;
    }


    /**
     * テーブル
     */
    public function table ($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * キャッシュリミット
     */
    public function expire ($time)
    {
        $this->options['cache-expire'] = $time;
        return $this;
    }

    /**
     * キャッシュ禁止
     */
    public function nocache ( )
    {
        $this->options['cache-until'] = time() - 100;
        return $this;
    }


    /**
     * Where句にイコールを追加
     *
     * @param string フィールド名
     * @param stringa
     * @return SqlBuilder
     */
    public function eq ($name, $value) 
    {
        $this->where([$name => ['=', $value, $this->getDeclearType(':'.$name)]]);
        return $this;
    }

    /**
     * Where
     *
     * $array (
     * field => arra('=', 'xxx','type'),'AND',field =>'array('xxx')')
     *
     */
    public function where ($array)
    {
        $this->wheres = $array;
        return $this;
    }

    private function parseWhere ($array)
    {
        $where = '';
        foreach ($array as $k=>$v) {
            if (!is_string($k)) {
                if (is_array($v)) {
                    $where.= ' ('.$this->parseWhere($v).') ';
                }else{
                    $where.= " $v";
                }
                continue;
            }
            $where .= sprintf(
                ' %s %s %s',
                $k, $v[0], $this->handler->escapeValue($v[1], $v[2])
            );
        }
        return $where;
    }

    /**
     * カラムのデクリエーションタイプを取得する
     *
     * @param string
     */
    private function getDeclearType($name)
    {
        return isset($this->declear_columns[$name]) ?
            $this->declear_columns[$name]:
            DB::DATA_TYPE_STR;
    }

    /**
     * データバインド
     *
     * @param string
     * @param mixed
     * @param string
     * @return SqlBuilder
     */
    public function bindValue ($place, $value, $type = null)
    {
        if ($type == null) {
            if (isset($this->declear_columns[$place])) {
                $type = $this->declear_columns[$place];
            } else {
                $type = DB::DATA_TYPE_STR;
            }
        }
        $this->values[$place] = [
            $value,
            $type
        ];

        return $this;
    }

    /**
     * データバインド
     *
     * @param array
     * @return SqlBuilder
     */
    public function bindValues ($array)
    {
        foreach ($array as $k=>$v) {
            if (is_array($v)) {
                $value = $v['value'];
                $type = $v['type'];
                $this->bindValue($k, $value, $type);
            }else{
                $this->bindValue($k, $value);
            }
        }

        return $this;
    }

    /**
     * SQLをビルドする
     *
     * @param array
     * @return string
     */
    public function buildSql ($params = [])
    {
        $type = $this->type;

        $func = 'build'.$type.'Sql';
        if (empty($type)) {
            throw new Exception\Exception(
                'ビルドタイプが指定されていません'
            );
        }
        if ( method_exists($this, $func)) {
            return call_user_func([$this,$func]);
        } else {
            throw new Exception\Exception([
                'ビルドタイプ %s 用のメソッドがありません',
                $type
            ]);
        }
    }

    private function buildSelectSql( )
    {
        if (empty($this->fields)) $this->fields = ['*'];
        $sql = sprintf(
            '%s %s FROM %s',
            $this->type,
            implode(',', $this->fields),
            $this->table
        );
        $sql.= $this->buildWhere();

        return $this->procBindings($sql);
    }

    private function buildInsertSql( )
    {
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(',', $this->fields),
            implode(',', $this->insert_values)
        );
        return $this->procBindings($sql);
    }

    private function buildUpdateSql( )
    {
        $set = '';
        $setParts = [];
        for ($i=0; $i<count($this->fields); $i++) {
            $setParts[] = sprintf(
                "%s = %s",
                $this->fields[$i],
                $this->insert_values[$i]
            );
        }
        $set = implode(', ', $setParts);

        $sql = sprintf(
            'UPDATE %s SET %s',
            $this->table,
            $set
        );
        $sql.= $this->buildWhere();

        return $this->procBindings($sql);
    }

    /**
     * SQLのWhere句をビルドする
     *
     * @return string
     */
    private function buildWhere ( )
    {
        if (empty($this->wheres)) return '';

        $sql = ' WHERE';
        $sql.= $this->parseWhere($this->wheres);
        return $sql;
    }

    /**
     * Valueバインドを処理する
     */
    private function procBindings ($sqlFormat)
    {
        $sql = preg_replace_callback (
            '/:[a-zA-Z_]+/x',
            function ($m) use ($sqlFormat){
                if (!isset($this->values[$m[0]])) {
                    throw new Exception\Exception([
                        '%sプレースに値がありません Format: %s',
                        $m[0],
                        $sqlFormat
                    ]);
                }
                $value = $this->values[$m[0]];
                return $this->handler->escapeValue($value[0], $value[1]);
            },$sqlFormat
            );
        return $sql;
    }

    /**
     * モデルクラスを設定する
     */
    public function setModelClass ($class)
    {
        $this->model_class = $class;
        return $this;
    }


    /**
     * 一つだけ取得する
     */
    public function first ( )
    {
        $result = $this->result()->fetchAssoc();
        $this->clear();
        return $result;
    }

    /**
     * 結果を保存し、返却する
     *
     * @return Result
     */
    public function result ( )
    {
        if (!$this->result) {
            $this->result = $this->execute();
        }

        return $this->result;
    }

    /**
     * SQLを実行し、結果を返却する
     *
     * @return Result
     */
    public function execute ( )
    {
        $query = $this->buildSql();
        $result =  $this->handler->query($query, $this->options);
        if ($this->model_class) {
            $result->setModelClass($this->model_class);
        }
        return $result;
    }
}
