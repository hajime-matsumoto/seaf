<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database\MongoDB;

use Seaf\Database;
use Seaf\Base\Module;
use Seaf\Base\Command;
use Seaf\Util\Util;

use Mongo;
use MongoLog;
use MongoClient;

/**
 * MongoDB操作ハンドラ
 */
class Handler extends Database\DatabaseHandler
{
    public $connection;
    public $db;
    private $lastInsertId;

    protected static $object_name = 'MongoDB';

    /**
     * コンストラクタ
     */
    public function __construct (Module\ModuleIF $parent, Database\DSN $dsn)
    {
        $this->setParentModule($parent);
        $params = $dsn->parse(true);
        $db = trim($params['db'],'/');

        $host = $params('host', 'localhost');
        $port = $params('port', '27017');

        try {
            if (class_exists('MongoClient')) {
                $this->connection = new \MongoClient("$host:$port");
                $this->db = $this->connection->$db;
            }else{
                $this->connection = new Mongo("$host:$port");
                $this->db = $this->connection->selectDB($db);
            }
        }catch (\MongoConnectionException $e) {
            echo $e;
            throw new Exception\CantConnectException([
                "MongoDBに接続できません"
            ]);
        }
    }

    /**
     * クエリを実行する
     */
    public function query ($query)
    {
        $result = mysqli_query($this->con, $query);
        if (!$result) {
            $this->warn(sprintf("[%s] %s", $query, $this->getLastError()));
        }else{
            $this->debug("Query OK $query");
        }

        if ($result === true || $result === false) {
            return new Result($result);
        } else {
            return new Cursor($result);
        }
    }

    /**
     * 最後のエラーを取得する
     */
    public function getLastError ( )
    {
        return mysqli_error($this->con);
    }

    /**
     * エスケープする
     */
    public function escapeVars ($datas)
    {
        if (!is_array($datas)) {
           return is_int($datas) ? intval($datas): mysqli_real_escape_string($this->con, $datas);
        }

        $safeVars = [];

        foreach ($datas as $k=>$v) {
            $safeVars[$k] = is_int($v) ? intval($v): mysqli_real_escape_string($this->con, $v);
        }
        return $safeVars;
    }

    /**
     * データを更新する
     */
    public function update ($table_name, $datas, $where, $limit = 1) 
    {
        $res = $this->db->$table_name->update($where,$datas,[
            'upsert'=>true
        ]);
        if ($res['err']) {
            $this->dump([
                'res'   => $res,
                'where' => $where,
                'datas' => $datas
            ]);
        }
    }

    /**
     * 配列からテーブルを作成する
     *
     * @param array
     */
    public function find($table, $array)
    {
        $c = Util::Dictionary($array);
        $cur = $this->db->$table->find($c->get('query'));

        if ($c->has('sort')) {
            $cur->sort($c->get('sort'));
        }
        if ($c->has('limit')) {
            $cur->limit($c->get('limit'));
        }
        if ($c->has('offset')) {
            $cur->offset($c->get('offset'));
        }

        return new Cursor($cur);
    }

    /**
     * 配列からテーブルを作成する
     *
     * @param array
     */
    public function createTable($table, $array)
    {
        $c = Util::Dictionary($array);

        if ($c->dict('options')->get('useDrop',false)) {
            $this->db->$table->drop();
            $this->info('MONGODB|DROP', $table);
        }

        foreach($c['indexes'] as $idx) {
            $indexes[$idx['name']] = 1;
        }
        //$this->info('MONGODB|CREATE_INDEX', print_r($indexes,true));

        $this->db->$table->ensureIndex($indexes);
        return true;
    }

    /**
     * 配列からINSERTを実行する
     *
     * @param name
     * @param array
     */
    public function insert($table_name, $datas)
    {
        $res = $this->db->$table_name->insert($datas);
        $this->lastInsertID = $datas['_id']->__toString();
        $this->debug("INSERT_ID ".$datas['_id']->__toString());
    }

    public function lastInsertId($table_name)
    {
        return $this->lastInsertID;
    }

}
