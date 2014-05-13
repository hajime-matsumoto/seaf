<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * データベースモジュール
 */
namespace Seaf\Database;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\BackEnd;

/**
 * モジュールファサード
 */
class Facade extends BackEnd\Module\ModuleFacade
{
    const DEFAULT_SOURCE_NAME = 'sql';

    private $datasources = [];

    public function initFacade ( )
    {
        $c = Util::Dictionary($this->root()->config->getConfig('database'));

        // データソースを設定する
        foreach ($c->get('datasources') as $name=>$ds) {
            $this->setDatasource($name, $ds);
        }
    }

    /**
     * データソースを設定する
     */
    public function setDatasource($name, $dsn_string)
    {
        $this->datasources[$name] = new DSN($dsn_string);
    }

    public function getDatasourceList( )
    {
        return $this->datasources;
    }

    /**
     * DatabaseHandlerを取得もしくは作成する
     */
    public function DBH($name)
    {
        if (isset($this->handlers[$name])) {
            return $this->handlers[$name];
        }
        if (!isset($this->datasources[$name])) {
            throw new InvalidDataBaseHandlerName($name);
        }

        $this->handlers[$name] = DatabaseHandler::factory($this->datasources[$name]);
        $this->handlers[$name]->addObserver($this);
        $this->handlers[$name]->setParent($this);

        return $this->handlers[$name];
    }

    public function isError($result)
    {
        return $result->isError();
    }


    /**
     * データソースを決定する
     */
    public function __proxyRequestGet(Proxy\ProxyRequestIF $request, $name)
    {
        if ($request->hasParam('ds')) {
            $ds = $request->getParam('ds');
            return $this->DBH($ds)->__proxyRequestGet($request, $name);
        }
        $new_request = clone $request;
        $new_request->setParam('ds', $name);
        return $new_request;
    }

    public function __proxyRequestCall(Proxy\ProxyRequestIF $request, $name, $params)
    {
        if ($request->hasParam('ds')) {
            $ds = $request->getParam('ds');
            return $this->DBH($ds)->__proxyRequestCall($request, $name, $params);
        }

        return parent::proxyRequestCall($request, $name, $params);
    }
}

class InvalidDataBaseHandlerName extends \Exception
{
    public function __construct($name)
    {
        parent::__construct("Invalid DB Handler Name $name");
    }
}
