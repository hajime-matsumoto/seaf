<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf;
use Seaf\Cache;

/**
 * 処理ハンドラ
 */
class Handler
{
    use Cache\HaveCacheHandler;

    /**
     * @var Con
     */
    private $con;

    /**
     * コンストラクタ
     */
    public function __construct (Con $con)
    {
        $this->con = $con;
    }

    // -------------------------------------
    // utility
    // -------------------------------------

    /**
     * ロガーを取得する
     */
    public function logger ( )
    {
        return Seaf::Logger('DB');
    }

    // -------------------------------------
    // SQL発行
    // -------------------------------------
    
    /**
     * クエリを実行
     *
     * @param string
     * @param array $options
     * @return Result
     */
    public function query ($sql, $options = [])
    {
        $this->logger()->debug('Query:'.$sql);

        // Alter,Create,Insert,Update,Deleteはキャッシュしない

        // SELECTはキャッシュする
        if (!preg_match('/(alter|create|insert|update|delete)/i', $sql)) {
            $expire = isset($options['cache-expire']) ? $options['cache-expire']: 0;
            $until = isset($options['cache-until']) ? $options['cache-until']: 0;

            // キャッシュが存在すればキャッシュを利用する
            if ($this->getCacheHandler( )->has($sql, $until)) {
                $this->logger()->debug('Query-Cache-Status: HIT');
                return $this->getCacheHandler()->getCachedData($sql);

            // キャッシュタイムが指定されていればキャッシュする
            } elseif ($expire !=0) {
                $result =  $this->realQuery($sql, $options);
                $this->logger()->debug('Query-Cache-Expires:'.$expire);

                // リザルトをキャッシュ可能にする
                return $this->getCacheHandler()->put(
                    $sql,
                    $expire,
                    $result->createCacheableResult()
                );
            }
        }

        return $this->realQuery($sql);
    }

    protected function realQuery($sql)
    {
        $result = new Result(
            $this,
            $this->con->query($sql)
        );

        if ($result->isError()) {
            $this->logger()->warn('Error:'.$result->getError());
        }
        return $result;
    }

    public function begin ( )
    {
        $this->realQuery('BEGIN');
    }

    public function commit ( )
    {
        $this->realQuery('COMMIT');
    }

    /**
     * 結果セットを必要としないクエリの実行
     *
     * @param string
     * @return bool
     */
    public function execute ($sql)
    {
        $res = $this->query($sql);
        return $res->isError() ? false: true;
    }

    /**
     * SQLステートメントを取得する
     */
    public function prepare ($sql)
    {
        return new Statement($this, $sql);
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
        return $this->con->escapeValue($value, $type);
    }

    // -------------------------------------
    // エラー管理
    // -------------------------------------

    /**
     * エラー判定
     *
     * @param mixed
     * @return bool
     */
    public function isError ($result)
    {
        return $this->con->isError($result);
    }

    /**
     * エラー取得
     *
     * @return string
     */
    public function getError ($result)
    {
        return $this->con->getError($result);
    }

    // -------------------------------------
    // レコード取得
    // -------------------------------------

    /**
     * 連想配列でレコードを取得
     *
     * @param mixed
     * @return array
     */
    public function fetchAssoc ($result)
    {
        return $this->con->fetchAssoc($result);
    }

    // -------------------------------------
    // モデル関係
    // -------------------------------------

    /**
     * スキーマからテーブルを生成
     */
    public function createTableBySchema ($schema)
    {
        $sql = $this->con->getCreateTableSQLBySchema($schema);
        $this->execute($sql);
    }

    /**
     * スキーマからテーブルを生成
     */
    public function dropTableBySchema ($schema)
    {
        $sql = $this->con->getDropTableSQLBySchema($schema);
        $this->execute($sql);
    }

    /**
     * テーブルを取得する
     */
    public function table ($name)
    {
        $table = new Table($name);
        $table->setHandler($this);
        return $table;
    }
}
