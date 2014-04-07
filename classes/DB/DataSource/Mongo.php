<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\DataSource;

use Seaf;
use Seaf\DB;
use Mongo as PHPMongo;

class Mongo extends DB\DataSource
{
    /**
     * @var \Mongo 
     */
    private $con;

    /**
     * @var \MongoDB
     */
    private $db;

    /**
     *
     */
    public function initDataSource (DB\DSN $dsn)
    {
        $params = $dsn->parse();
        $db = trim($params['db'],'/');

        try {
            $this->con = new PHPMongo( );
            $this->db = $this->con->selectDB($db);
        }catch (\MongoConnectionException $e) {
            throw new Exception\Exception([
                "MongoDBに接続できません"
            ]);
        }
    }

    /**
     * INSERT REQUEST時の処理
     *
     * @param DB\Request
     */
    public function insertRequest (DB\Request $req)
    {
        // テーブル名を取得
        $table = $this->requestTable($req);

        // インサート
        $res = $this->db->$table->insert($req->getParams());

        Seaf::Logger('DB')->debug('MongoDB-INSERT-TO: '.$table);

        return $res;
    }

    /**
     * 検索時の処理
     *
     * @param DB\Request
     */
    public function findRequest (DB\Request $req)
    {
        // テーブル名を取得
        $table = $this->requestTable($req);

        $params = $req->getParams();
        $cursor = $this->db->$table->find($this->buildWhere($req->getWhere()));

        $order = $req->getOrder();
        if (!empty($order)) {
            $sort = [];
            foreach($req->getOrder() as $k=>$v){
                $sort[$k] = $v == 'asc' ? 1: -1;
            }
            $cursor->sort($sort);
        }

        if ($req->getLimit() > 0) {
            $cursor->limit($req->getLimit());
        }
        return $cursor;
    }

    /**
     * コマンドの処理
     *
     * @param DB\Request
     */
    public function commandRequest (DB\Request $req)
    {
        // テーブル名を取得
        try {
            $table = $this->requestTable($req);
            $params = $req->getParams();
            $res = [];
            if (isset($params['drop']) && $params['drop'] === true) {
                $res['drop'] = $this->db->$table->drop();
            }
            if (isset($params['createIndex'])) {
                $res['createIndex'] = $this->db->$table->ensureIndex($params['createIndex']);
            }
            return $res;
        } catch (\Exception $e) {
            $params = $req->getParams();
            $res = $this->db->command($req->getParams());
            return $this->db->selectCollection($res['result'])->find();
        }
    }

    /**
     * 更新リクエストの処理
     *
     * @param DB\Request
     */
    public function updateRequest (DB\Request $req)
    {
        // テーブル名を取得
        $table = $this->requestTable($req);
        $params = $req->getParams();

        $res = $this->db->$table
            ->update(
                $this->buildWhere($req->getWhere()),
                ['$set'=>$params],
                ($req->getOptions() ? $req->getOptions(): ["multiple" => true])
            );
        return $res;
    }

    /**
     * 結果のエラー判定
     */
    public function isError ($result)
    {
        return $result === false? true: false;
    }

    /**
     */
    public function fetchAssoc ($result)
    {
        return $result->getNext();
    }

    public function buildWhere($where)
    {
        foreach ($where as $k=>$v)
        {
            if (is_array($v)) {
                list($flag,$value) = [key($v),current($v)];
                if ($flag == '$regex') {
                    $v = new \MongoRegex($value);
                }
            }
            $where[$k] = $v;
        }
        return $where;
    }

    public function createTable(DB\Schema $schema, $drop = false)
    {
        $table = $schema->table;
        $index = [];
        foreach($schema->indexes as $k=>$v)
        {
            $index[$v['field']] = true;
        }

        if ($drop == true) {
            $this->db->$table->drop();
        }

        $this->db->$table->ensureIndex($index);
    }
}
