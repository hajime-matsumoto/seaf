<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\MongoDB;

use Seaf\Base;
use Seaf\Data;
use Seaf\Registry\Registry;

use Mongo;
use MongoLog;
use MongoClient;

class MongoDBHandler extends Data\DB\ProductHandler
{
    use Base\SingletonTrait;

    public $connection;
    public $db;

    public static function who ( )
    {
        return __CLASS__;
    }

    /**
     * コンストラクタ
     *
     * @param string|array
     */
    public function __construct (Data\DSN $dsn)
    {
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

        if (Registry::isDebug()) {
            MongoLog::setLevel(MongoLog::ALL ^ MongoLog::FINE);
            MongoLog::setModule(MongoLog::ALL);
        }
    }


    /**
     * テーブルを取得する
     */
    public function getTable($name)
    {
        return new Table($this, $name);
    }

    /**
     * 直近のデータを取得する
     */
    public function getLastError( )
    {
        return $this->db->lastError();
    }

    protected function makeResult ($result)
    {
        return new Result($result,$this->getLAstError());
    }
}
