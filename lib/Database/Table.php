<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Database;

use Seaf\BackEnd\Module;
use Seaf\Util\Util;
use Seaf\Base\Proxy;

/**
 * Table Class
 */
class Table implements Module\ModuleFacadeIF
{
    use Module\ModuleFacadeTrait;


    private $name;

    /**
     * コンストラクタ
     */
    public function __construct ($name)
    {
        $this->name = $name;
    }

    public function __proxyRequestCall(Proxy\ProxyRequestIF $request, $name, $params)
    {
        if ($name == 'declear' || $name == 'find') {
            array_unshift($params, $request);
        }
        return $this->proxyRequestCall($request, $name, $params);
    }

    //--------------------------------------------
    // 自前で処理するリクエスト
    //--------------------------------------------

    /**
     * DeclearRequestを発行する
     */
    public function declear(Proxy\ProxyRequestIF $request)
    {
        return $request->factory(__NAMESPACE__.'\\Request\DeclearRequest');
    }

    /**
     * FindRequestを発行する
     */
    public function find(Proxy\ProxyRequestIF $request, $query = [])
    {
        $findRequest = $request->factory(__NAMESPACE__.'\\Request\FindRequest');
        $findRequest->query($query);
        return $findRequest;
    }

    //--------------------------------------------
    // 実行系
    //--------------------------------------------

    /**
     * 配列からテーブルを作成する
     */
    public function createTable($settings)
    {
        // テーブル名を追加する
        $settings['name'] = $this->name;

        // CreateTableを実行する
        return $this->getParent()->createTable($settings);
    }

    /**
     * 配列からテーブルを検索する
     */
    public function findTable($settings)
    {
        // テーブル名を追加する
        $settings['name'] = $this->name;

        // CreateTableを実行する
        return $this->getParent()->findTable($settings);
    }

    /**
     * Insertを実行
     */
    public function insert($datas, &$lastInsertId = false)
    {
        $result = $this->getParent()->insert($this->name, $datas);
        return $result;
    }

    /**
     * updateを実行
     */
    public function update($datas, $query)
    {
        $result = $this->getParent()->update($this->name, $datas, $query);
        return $result;
    }

    /**
     * Last Insert IDを取得
     */
    public function lastInsertId( )
    {
        return  $this->handler->lastInsertId($this->name);
    }
}
