<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DataSource\Handler;

use Seaf;
use Seaf\Base;
use Seaf\DataSource;

use Mongo;
use MongoClient;

/**
 * MongoDB用のハンドラ
 */
class MongoHandler extends DataSource\DataSourceHandler
{
    private $con;
    private $db;

    /**
     * コンストラクタ
     *
     * @param string|array
     */
    public function __construct (DataSource\DSN $dsn, DataSource\DataSource $ds)
    {
        parent::__construct($dsn, $ds);

        $params = $dsn->parse();
        $db = trim($params['db'],'/');

        try {
            if (class_exists('MongoClient')) {
                $this->con = new \MongoClient( );
                $this->db = $this->con->$db;
            }else{
                $this->con = new Mongo( );
                $this->db = $this->con->selectDB($db);
            }
        }catch (\MongoConnectionException $e) {
            echo $e;
            throw new Exception\Exception([
                "MongoDBに接続できません"
            ]);
        }
    }

    /**
     * テーブル作成
     */
    public function createTable(DataSource\Schema $schema, $drop = false)
    {
        $table = $schema->table;
        $index = [];
        foreach($schema->indexes as $k=>$v)
        {
            $index[$v['field']] = true;
        }

        if ($drop == true) {
            $res['drop'] = $this->db->$table->drop();
            $this->debug([
                'msg'=>'Drop Tabe',
                'table'=>$table,
                'res'=>$res['drop']
            ]);
        }

        $res['index'] = $this->db->$table->ensureIndex($index);
            $this->debug([
                'msg'=>'Ensure Index',
                'table'=>$table,
                'res'=>$res['index']
            ]);

        $method = 'CREATE_TABLE';
        return $this->createResult($res, []);
    }

    /**
     * Fetch
     */
    public function fetch ($result)
    {
        return $result->getNext();
    }


    /**
     * 検索
     *
     * @param Request
     */
    public function findRequest(DataSource\Request $req)
    {
        // テーブル名を取得
        $table = $req->getPath( );

        // 検索条件を取得
        $where = $this->buildWhere($req->getWhereParts());

        // ソート条件を取得
        $order = $req->getOrder();

        // リミットを取得
        $limit = $req->getLimit();

        // モンゴＤＢの検索カーソルをオープン
        $cur = $this->db->$table->find($where);

        if (!empty($order)) {
            $sort = [];
            foreach($order as $k=>$v) $sort[$k] = $v == 'asc' ? 1: -1;
            $cur->sort($sort);
        }

        if ($limit > 0) {
            $cur->limit($req->getLimit());
        }

        $error = $this->db->lastError();

        $method = $req->getMethod();
        return new DataSource\Result(
            $cur,
            $this,
            !empty($error['err']),
            $error['err'],
            $request_log = compact('method', 'table','where','order','limit')
        );
    }


    /**
     * 更新リクエストの処理
     *
     * @param DB\Request
     */
    public function updateRequest (DataSource\Request $req)
    {
        // テーブル名を取得
        $method = $req->getMethod();
        $table = $req->getPath();
        $params = $req->getParams();
        // 検索条件を取得
        $where = $this->buildWhere($req->getWhereParts());
        $options = $req->getOptions() ? $req->getOptions(): ["multiple" => true];

        if (isset($params['_id'])) {unset($params['_id']);}


        $res = $this->db->$table->update($where,['$set'=>$params],$options);
        $error = $this->db->lastError();
        $request_log = compact('method','table','where','options','params');


        return $this->createResult($res, $request_log);
    }

    /**
     * コマンドの処理
     *
     * @param DB\Request
     */
    public function commandRequest (DataSource\Request $req)
    {
        $isError = false;
        $errorMsg = [];

        // テーブル名を取得
        if ('/' !== $req->getPath()) {
            $table = $req->getPath( );
            $params = $req->getParams();
            $res = [];


            if (isset($params['drop']) && $params['drop'] === true) {
                $res['drop'] = $this->db->$table->drop();
                $error = $this->db->lastError();
                if (!empty($error['err'])) {
                    $isError = true;
                    $errorMsg[] = $error['err'];
                }
            }
            if (isset($params['createIndex'])) {
                $res['createIndex'] = $this->db->$table->ensureIndex($params['createIndex']);
                $error = $this->db->lastError();
                if (!empty($error['err'])) {
                    $isError = true;
                    $errorMsg[] = $error['err'];
                }
            }
            return $this->createResult($res, ['params'=>$params], $isError, $errorMsg);
        } else {
            $params = $req->getParams();

            $res = $this->db->command($req->getParams());
            $error = $this->db->lastError();
            if (!empty($error['err'])) {
                $isError = true;
                $errorMsg[] = $error['err'];
            }

            $res = $this->db->selectCollection($res['result'])->find();
            $error = $this->db->lastError();
            if (!empty($error['err'])) {
                $isError = true;
                $errorMsg[] = $error['err'];
            }

            return $this->createResult($res, [], $isError, $errorMsg);
        }
    }

    /**
     * INSERT REQUEST時の処理
     *
     * @param DB\Request
     */
    public function insertRequest (DataSource\Request $req)
    {
        // テーブル名を取得
        $table = $req->getPath();
        $params = $req->getParams();

        // インサート
        $res = $this->db->$table->insert($params);

        $res = $this->createResult($res, compact('table','params'));
        $res->lastInsertId = false;
        return $res;
    }


    private function createResult($res, $log = [], $isError = null, $errorMsg = null)
    {
        if ($isError === null) {
            $error = $this->db->lastError();
            $isError = !empty($error['err']);
            $errorMsg = $error['err'];
        }

        return new DataSource\Result(
            $res,
            $this,
            $isError,
            $errorMsg,
            $log
        );
    }

    /**
     * Whereをビルドする
     */
    private function buildWhere($parts)
    {
        $where = [];
        foreach ($parts as $part) {
            foreach ($part as $k=>$v) {
                if (is_array($v)) {
                    list($flag,$value) = [key($v),current($v)];
                    if ($flag == '$regex') {
                        $part[$k] = new \MongoRegex($value);
                    }
                }
            }
            $where = array_merge($where, $part);
        }
        return $where;
    }

}
