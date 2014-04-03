<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB;

use Seaf\Exception;

/**
 * データソース
 */
abstract class DataSource
{
    /**
     * Handler
     * @var Handler;
     */
    private $handler;

    /**
     * 処理区分マップ
     * @var array
     */
    protected $procMap = [
        'QUERY'   => 'queryRequest',
        'DELETE'   => 'deleteRequest',
        'INSERT'  => 'insertRequest',
        'FIND'    => 'findRequest',
        'UPDATE'  => 'updateRequest',
        'COMMAND' => 'commandRequest'
    ];

    /**
     * データソースを作成
     *
     * @param DSN
     */
    public static function factory (DSN $dsn, Handler $handler)
    {
        $type = $dsn->getType();
        $class = __NAMESPACE__.'\\DataSource\\'.ucfirst($type);
        if (!class_exists($class)) {
            throw new Exception\Exception ([
                "データソースクラス %s が存在しません",
                $class
            ]);
        }
        $ds = new $class($dsn);
        $ds->handler = $handler;
        return $ds;
    }

    /**
     * リクエストを作成する
     *
     * @param string 処理区分 (QUERY|INSERT|UPDATE...)
     */
    public function newRequest ($type)
    {
        $request = $this->handler->newRequest($type);
        $request->setTarget($this);
        return $request;
    }

    /**
     * コンストラクタ
     */
    public function __construct (DSN $dsn)
    {
        $this->initDataSource($dsn);
    }

    /**
     * リクエストを処理する
     *
     * @param Request
     */
    public function request (Request $request)
    {
        $type = $request->getType();

        // 処理区分に応じたメソッドを実行する
        if (isset($this->procMap[$type])) {
            $method = $this->procMap[$type];
            return $this->$method($request);
        }
        throw new Exception\Exception ([
            "リクエストタイプ %s は処理が定義されていません",
            $type
        ]);
    }

    protected function requestTable (Request $request)
    {
        // テーブル名を取得
        $table = $request->getTargetTable();

        if (!$table) {
            throw new Exception\Exception([
                "テーブル名は必須です"
            ]);
        };

        return $table;
    }

    /**
     * GET Tableをつなぐ
     */
    public function __get($name) {
        $table = $this->handler->getTable($name);
        $table->setDataSource($this);
        return $table;
    }
}
