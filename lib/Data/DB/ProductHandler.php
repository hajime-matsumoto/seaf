<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Data\DB;

use Seaf\Base;
use Seaf\Data;
use Seaf\Registry\Registry;

/**
 * データベースハンドラ
 */
abstract class ProductHandler
{
    /**
     * テーブルを取得する
     */
    abstract public function getTable($name);

    /**
     * 結果
     */
    abstract protected function makeResult($result);

    /**
     * テーブルを取得する
     */
    public function __get($name)
    {
        return $this->getTable($name);
    }


    /**
     * 直近のデータを取得する
     */
    public function getLastError( )
    {
        return $this->db->lastError();
    }

    /**
     * リクエストを処理する
     */
    public function executeRequest ($request)
    {
        $type = $request->getType();
        $result = $this->{'execute'.ucfirst($type).'Request'}($request);
        return $result;
    }

    /**
     * INSERTリクエストを処理する
     */
    public function executeInsertRequest ($request)
    {
        $tableName = $request->getTableName();
        $result = $this->$tableName->insert($request->getParams());
        return $this->makeResult($result, $this->getLastError());
    }

    /**
     * DROPリクエストを処理する
     */
    public function executeDropRequest ($request)
    {
        $tableName = $request->getTableName();
        $result = $this->$tableName->drop( );
        return $this->makeResult($result, $this->getLastError());
    }

    /**
     * CREATEリクエストを処理する
     */
    public function executeCreateRequest ($request)
    {
        $tableName = $request->getTableName();
        $result = $this->$tableName->create($request->getParam('schema'));
        return $this->makeResult($result, $this->getLastError());
    }

    /**
     * Findリクエストを処理する
     */
    public function executeFindRequest ($request)
    {
        $tableName = $request->getTableName();
        $result = $this->$tableName->realFind($request->getParam('findQuery'));
        return $result;
    }

}
