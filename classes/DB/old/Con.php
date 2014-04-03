<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;


abstract class Con
{
    /**
     * @var DSN
     */
    protected $dsn;

    /**
     * コンストラクタ
     *
     * @param DSN
     */
    public function __construct (DSN $dsn)
    {
        $this->dsn = $dsn;

        // 初期化
        $this->initCon($this->dsn);
    }

    /**
     * 初期化
     *
     * @param DSN
     */
    abstract protected function initCon (DSN $dsn);

    /**
     * 処理ハンドラを取得する
     */
    public function handler ( )
    {
        return new Handler($this);
    }

    // -------------------------------------
    // モデル関係
    // -------------------------------------

    /**
     * スキーマからCreateTable文を作成
     *
     * @param Schema
     * @return string
     */
    public function getCreateTableSQLBySchema ($schema)
    {
        $sql = sprintf('CREATE TABLE IF NOT EXISTS %01$s ('."\n", $schema('table'));

        $cols = array();
        foreach ($schema('cols') as $name => $col) {
            $cols[] = "$name ".$this->createTableColSql($col);
        }
        $sql.= implode(",\n", $cols);
        $sql.= "\n);";

        return $sql;
    }

    /**
     * スキーマからDropTable文を作成
     *
     * @param Schema
     * @return string
     */
    public function getDropTableSQLBySchema ($schema)
    {
        $sql = sprintf('DROP TABLE IF EXISTS %01$s;', $schema('table'));

        return $sql;
    }
}
