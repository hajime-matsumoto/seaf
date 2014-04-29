<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Data;

/**
 * データベースハンドラ
 */
class DBHandler
{
    private $defaultConnectionName = 'default';

    private $connectionMap;
    private $tableMap;
    private $handlerList = [];

    /**
     * テーブルを取得する
     *
     * @param string
     * @return Table
     */
    public function __get($name)
    {
        return $this->getTable($name);
    }

    /**
     * デフォルトのコネクション名
     *
     * @param string
     * @return DBHandler
     */
    public function setDefaultConnectionName($name)
    {
        $this->defaultConnectionName = $name;
        return $this;
    }

    /**
     * コネクション名とDSNのマップ
     *
     * @param array
     * @return DBHandler
     */
    public function connectionMap($map)
    {
        foreach ($map as $k=>$v) {
            $this->connectionMap[$k] = $v;
        }
        return $this;
    }

    /**
     * コネクションとテーブルのマップ
     *
     * @param array
     * @return DBHandler
     */
    public function tableMap($map)
    {
        foreach ($map as $k=>$v) {
            $this->tableMap[$k] = $v;
        }
        return $this;
    }

    /**
     * テーブルを取得する
     *
     * @param string
     * @return Table
     */
    public function getTable ($name)
    {
        return new Table($this, $name);
    }

    /**
     * リクエストを実行する
     *
     * @param Request
     * @return Result
     */
    public function execute (Request $Request)
    {
        $handler = $this->getRealHandlerByTableName($Request->getTableName());
        return $handler->executeRequest($Request);
    }

    protected function getRealHandlerByTableName($name)
    {
        if (isset($this->tableMap[$name])) {
            return $this->getRealHandler($this->tableMap[$name]);
        }
        return $this->getRealHandler($this->defaultConnectionName);
    }

    protected function getRealHandler($name)
    {
        if (!isset($this->handlerList[$name])) {
            $this->handlerList[$name] = $this->buildHandler($this->connectionMap[$name]);
        }
        return $this->handlerList[$name];
    }

    protected function buildHandler($dsn)
    {
        $dsn = new Data\DSN($dsn);
        $type = $dsn->getType();
        return $this->{'build'.ucfirst($type).'Handler'}($dsn);
    }

    protected function buildMongoDBHandler($dsn)
    {
        $handler = new Data\MongoDB\MongoDBHandler($dsn);
        return $handler;
    }

    protected function buildMysqlHandler($dsn)
    {
        $handler = new Data\Mysql\MysqlHandler($dsn);
        return $handler;
    }
}
