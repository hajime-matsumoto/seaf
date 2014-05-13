<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * データベースモジュール
 */
namespace Seaf\Database;

use Seaf\Util\Util;
use Seaf\Base\Module;
use Seaf\Base\Proxy;
use Seaf\Base\ConfigureTrait;
use Seaf\Base\Component;

class TableHandler extends Module\ModuleFacade
{
    protected static $object_name = 'Table';

    //
    // リクエストを再送する仕組み
    //
    public function __proxyRequestCall(Proxy\ProxyRequestIF $req, $name, $params)
    {
        $result = parent::__proxyRequestCall($req, $name, $params);
        if($result->retrive() instanceof Proxy\ProxyRequestIF) {
            $result->retrive()->copyRequest($req);
        }
        return $result;
    }

    /**
     * 検索クエリを発行する
     */
    public function find($query = [])
    {
        $findRequest = new ProxyRequest\TableFindRequest( );
        $findRequest->query($query);
        return $findRequest;
    }

    /**
     * Declearクエリを発行する
     */
    public function declear( )
    {
        return new ProxyRequest\TableDeclearRequest();
    }

    /**
     * Insertを実行
     */
    public function insert($table, $datas, &$lastInsertId = false)
    {
        $result = $this->getParent()->getHandlerByTable($table)->insert($table, $datas);
        return $result;
    }

    /**
     * updateを実行
     */
    public function update($table, $datas, $query)
    {
        $result = $this->getParent()->getHandlerByTable($table)->update($table, $datas, $query);
        return $result;
    }

    /**
     * Last Insert IDを取得
     */
    public function lastInsertId( )
    {
        return  $this->handler->lastInsertId($this->name);
    }


    /**
     * 検索クエリを実行する
     */
    public function getCursor($table, $array)
    {
        return $this->getParent( )->getHandlerByTable($table)->find($table, $array);
    }

    /**
     * クリエイトTableを実行する
     */
    public function createTable($table, $array)
    {
        return $this->getParent( )->getHandlerByTable($table)->createTable($table, $array);
    }
}
