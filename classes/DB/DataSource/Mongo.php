<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\DataSource;

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
        $cursor = $this->db->$table->find($params);

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
        //$table = $this->requestTable($req);
        $params = $req->getParams();

        // $req->commandType(); 
        /*
        $res = $this->db->command(array(
            'mapreduce' => $table,
            'map' => $params['map'],
            'reduce'=>$params['reduce'],
            'out' => $params['out']
        ));
         */
        // $req->commandType();
        $res = $this->db->command($req->getParams());
        return $this->db->selectCollection($res['result'])->find();
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
}
